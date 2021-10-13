<?php

namespace Haxibiao\Store\Traits;

use App\Aso;
use App\Order;
use App\Store;
use GraphQL\Type\Definition\ResolveInfo;
use Haxibiao\Breeze\Exceptions\GQLException;
use Haxibiao\Breeze\Exceptions\UserException;
use Haxibiao\Breeze\User;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

trait OrderResolvers
{
    //修改订单状态
    public function resolveUpdateOrderStatus($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if ($user = currentUser()) {
            $order_id = $args['order_id']; //订单id
            $status   = $args['status']; //状态

            if (!in_array($status, [Order::RESERVE, Order::REJECT, Order::CANCEL, Order::ACCEPT, Order::WORKING, Order::OVER])) {
                throw new UserException("暂不支持其他操作");
            }

            $order = Order::findOrFail($order_id);
            return $order->update(['status' => $status]);
        } else {
            throw new UserException("客户端没有登录。。。");
        }
    }

    //预约技师
    public function resolveReserveTechnicianUser($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if ($user = currentUser()) {
            $product_id         = $args['product_id'] ?? null; //服务
            $technician_user_id = $args['technician_user_id']; //技师
            $appointment_time   = $args['appointment_time'] ?? null; //预约时间
            return Order::reserveTechnicianUser($user, $product_id, $technician_user_id, $appointment_time);
        } else {
            throw new UserException("客户端没有登录。。。");
        }
    }

    //下单
    public function makeOrder($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if ($user = currentUser()) {
            $product_id = $args['product_id']; //商品
            return Order::createOrder($user, $product_id);
        } else {
            throw new UserException("客户端没有登录。。。");
        }
    }

    //下单
    public function makeGameOrder($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if ($user = currentUser()) {
            $product_id = $args['product_id']; //商品
            $item_id    = $args['item_id'] ?? null; //抵用券（金币）
            //2. 判断版本号
            $dimension  = optional($args)['dimension']; //规格:皮肤，角色。。。
            $dimension2 = optional($args)['dimension2']; //规格:时长1小时，2小时
            if (is_null($dimension) || is_null($dimension2)) {
                throw new GQLException('当前版本过低,请更新后再尝试租号,详情咨询QQ群:' . Aso::getValue("功能页", "动态修改群qq号"));
            }
            return Order::createGameOrder($product_id, $item_id, $dimension, $dimension2);
        } else {
            throw new GQLException("客户端没有登录。。。");
        }
    }

    //获取我的订单
    public function getMyOrders($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if ($user = currentUser()) {
            return Order::where("user_id", $user->id)->orderBy("created_at", "desc");
        } else {
            throw new GQLException("客户端没有登录。。。");
        }
    }

    //退单
    public function resovleBackOrder($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if ($user = currentUser()) {
            return Order::backOrder($args['order_id'], $args['content'] ?? "无理由", $args['images'] ?? null, $args['image_urls'] ?? null);
        } else {
            throw new GQLException("客户端没有登录。。。");
        }
    }

    public function resolveOrders($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $status    = $args['status'] ?? null;
        $user      = getUser();
        $store_ids = Store::where('user_id', $user->id)->pluck('id')->toArray();
        return Order::query()
            ->when(count($store_ids), function ($qb) use ($store_ids) {
                return $qb->whereIn('store_id', $store_ids);
            })
            ->when($status, function ($qb) use ($status) {
                return $qb->where('status', $status);
            })->orderByDesc('created_at');
    }

    //查询正在进行中的订单
    public function resolveWorkingOrder($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = User::findOrFail($args['user_id']);
        return Order::query()
            ->where('technician_id', $user->id)
            ->where('status', Order::WORKING)
            ->orderByDesc('created_at')->first();
    }

}
