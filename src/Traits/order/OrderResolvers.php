<?php

namespace Haxibiao\Store\Traits;

use App\Aso;
use App\Exceptions\GQLException;
use App\Order;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

trait OrderResolvers
{
    //下单
    public function makeOrder($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if ($user = checkUser()) {
            $product_id = $args['product_id']; //商品
            $item_id    = $args['item_id'] ?? null; //抵用券（金币）
            //2. 判断版本号
            $dimension  = optional($args)['dimension']; //规格:皮肤，角色。。。
            $dimension2 = optional($args)['dimension2']; //规格:时长1小时，2小时
            if (is_null($dimension) || is_null($dimension2)) {
                throw new GQLException('当前版本过低,请更新后再尝试租号,详情咨询QQ群:' . Aso::getValue("功能页", "动态修改群qq号"));
            }
            return $this->createOrder($product_id, $item_id, $dimension, $dimension2);
        } else {
            throw new GQLException("客户端没有登录。。。");
        }
    }

    //获取我的订单
    public function getMyOrders($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if ($user = checkUser()) {
            return Order::where("user_id", $user->id)->orderBy("created_at", "desc");
        } else {
            throw new GQLException("客户端没有登录。。。");
        }
    }

    //退单
    public function resovleBackOrder($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if ($user = checkUser()) {
            return Order::backOrder($args['order_id']);
        } else {
            throw new GQLException("客户端没有登录。。。");
        }
    }
}
