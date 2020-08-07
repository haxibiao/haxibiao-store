<?php

namespace Haxibiao\Store\Traits;

use App\Gold;
use App\Image;
use App\Order;
use Exception;
use App\Refund;
use App\Product;
use App\PlatformAccount;
use App\Exceptions\GQLException;
use Haxibiao\Helpers\BadWordUtils;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Haxibiao\Store\Jobs\OrderAutoExpire;
use Haxibiao\Store\Notifications\PlatformAccountExpire;

trait OrderRepo
{
    public function createOrder($product_id, $item_id, $dimension, $dimension2 = 1)
    {
        //规格1:皮肤名   dimension
        //规格2:租借时间   dimension2
        $user = getUser();

        //是否下架
        $product = Product::where("id", $product_id)
            ->where("status", 1)
            ->first();
        if (empty($product)) {
            throw new GQLException("该商品已下架！");
        }

        //是否还有库存
        $platform_account = PlatformAccount::where("product_id", $product_id)
            ->where("order_status", PlatformAccount::UNUSE)
            ->where("dimension", $dimension)
            ->where("dimension2", $dimension2)
            ->first();

        if (empty($platform_account)) {
            throw new GQLException("该规格的账号没有啦！请选择其他规格吧！");
        }

        if (empty($user) || is_null($user->phone)) {
            throw new GQLException("请先去绑定手机号再来租号吧！");
        }

        DB::beginTransaction();
        //使用金币抵用券
        $item_value = 0;
        if ($item_id) {
            $item = $user->items()->find($item_id);
            if ($item) {
                $item_value = $item->value;
            }
        }
        if ($user->gold + $item_value < $platform_account->price) {
            throw new GQLException('您的金币不足!');
        }
        try {
            //FIXME::紧急修复租号时报错问题，这段代码不优雅~,有空改
            if ($item_id) {
                $item = $user->items()->find($item_id);
                if ($item) {
                    $user->items()->detach($item_id);
                }
            }

            //每买一次数量-1
            $product->update(["available_amount", $product->available_amount - 1]);

            // 租号
            $order = $this->freeBorrow($product, $platform_account, $dimension2, $item_value);
            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            throw new GQLException("未知异常，订单取消");
        }
    }

    //免费租号
    public function freeBorrow($product, $platform_account, $dimension2, $item_value)
    {
        $user = getUser();
        //新人只能参与免费租号一次
        //新人免费租号换成奖励”租号抵用券“（道具）
        // $order = Order::where("user_id", $user->id)->first();
        // if (!empty($order) && $platform_account->price == 0) {
        //     throw new GQLException("新人只能参与一次免费租号活动");
        // }
        //1.取出'未使用'的账号返回给用户
        //上面已经拿到了platform_account

        //2.创建订单
        $order = Order::create([
            "user_id"            => $user->id,
            "number"             => time(),
            //nova查询某个订单租借了哪个账号需要
            "platformAccount_id" => $platform_account->id,
            "status"             => Order::PAID,
        ]);

        //3.更新order和product的关联
        $order->products()->syncWithoutDetaching([
            $product->id => [
                'amount' => 1,
                'price'  => $platform_account->price,
            ],
        ]);

        //4.更新账号为使用中
        $platform_account->update([
            "order_status" => PlatformAccount::INUSE,
            "order_id"     => $order->id,
            "user_id"      => $user->id,
        ]);

        //5.扣除智慧点 item_value抵用券

        Gold::makeOutcome($user, $platform_account->price - $item_value, '租借账号扣除');

        //6.通知用户(订单剩余十分钟)
        $user->notify((new PlatformAccountExpire($platform_account))->delay(now()
            ->addHour($dimension2)->subMinutes(10)));
        //更新订单和账号状态为已过期
        \dispatch(new OrderAutoExpire($platform_account, $order))
            ->delay(now()->addHour($dimension2));
        return $order;
    }

    public static function backOrder($order_id, $content, $images, $image_urls)
    {
        app_track_event('用户页', '订单退款');

        $user    = getUser();
        $order = Order::find($order_id);
        if (empty($order)) {
            throw new GQLException("不存在该订单！");
        }

        if ($order->status != Order::PAID) {
            throw new GQLException("该订单已过期");
        }
        //TODO:要防止用户一直刷退单接口的行为
        if ($order->created_at->diffInSeconds(now(), false) > Order::REFUND_TIME) {
            throw new GQLException("下单已超过十分钟，退单失败！");
        }

        if (BadWordUtils::check($content)) {
            throw new GQLException('退款理由中含有包含非法内容,请删除后再试!');
        }
        $refund = Refund::firstOrCreate([
            'user_id' => $user->id,
            'order_id' => $order_id,
            'content' => $content,
        ]);

        if (!empty($images)) {
            foreach ($images as $image) {
                $image = Image::saveImage($image);
                $refund->images()->attach($image->id);
            }
        }

        if (!empty($image_urls) && is_array($image_urls)) {
            $image_ids = array_map(function ($url) {
                return intval(pathinfo($url)['filename']);
            }, $image_urls);
            $refund->images()->sync($image_ids);
        }
        $refund->save();

        return true;
    }
}
