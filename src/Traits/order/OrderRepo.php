<?php

namespace haxibiao\store\Traits;

use App\Gold;
use App\Order;
use Exception;
use App\Product;
use App\PlatformAccount;
use App\Exceptions\GQLException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use haxibiao\store\Jobs\OrderAutoExpire;
use haxibiao\store\Notifications\PlatformAccountExpire;

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

        //使用金币抵用券
        $item_value = 0;
        if ($item_id) {
            $item = $user->items()->find($item_id);
            if ($item) {
                $item_value = $item->value;
                $user->items()->detach($item_id);
            }
        }
        if ($user->gold + $item_value < $platform_account->price) {
            throw new GQLException('您的金币不足!');
        }

        if (empty($user) || is_null($user->phone)) {
            throw new GQLException("请先去绑定手机号再来租号吧！");
        }

        //每买一次数量-1
        $product->update(["available_amount", $product->available_amount - 1]);

        // 租号
        return $this->freeBorrow($product, $platform_account, $dimension2, $item_value);
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
        DB::beginTransaction();
        try {
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

            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            throw new GQLException("未知异常，订单取消");
        }
    }

    public static function backOrder($order_id)
    {
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
        DB::BeginTransaction();
        try {
            $order->status = Order::EXPIRE;
            $order->save();
            //更改关联账号状态为2（过期）
            $platformAccounts = $order->platformAccount;
            $gold             = 0;
            foreach ($platformAccounts as $platformAccount) {
                $gold += $platformAccount->price;
                $platformAccount->order_status = PlatformAccount::EXPIRE;
                $platformAccount->save();
            }
            //退款给用户
            Gold::makeIncome($order->user, $gold, "退款返回");
            DB::commit();
        } catch (Exception $e) {
            throw new GQLException("发生未知错误，退款失败！");
            DB::rollback();
        }
        return true;
    }
}
