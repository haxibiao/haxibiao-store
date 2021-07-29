<?php

namespace Haxibiao\Store\Traits;

use App\Store;
use GraphQL\Type\Definition\ResolveInfo;
use Haxibiao\Breeze\Exceptions\GQLException;
use Haxibiao\Content\Location;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

trait StoreResolvers
{

    //创建店铺信息
    public function resolveCreateStore($root, $args, $context, $resolveInfo)
    {
        $user = getUser();
        app_track_event("用户", "创建店铺");
        $store = Store::firstOrNew([
            'user_id' => $user->id,
            'name'    => $args['description'],
        ]);
        $location = data_get($args, 'location') ?? null;
        $store->fill(array_except($args, ['location']))->save();
        if ($location) {
            Location::storeLocation($location, 'stores', $store->id);
        }

    }

    //根据用户id查询店铺
    public function getStores($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if ($user = currentUser()) {
            $store = Store::with("product")
                ->where('user_id', $args['user_id'])->where("status", 1)->first();
            if (empty($store)) {
                throw new GQLException("该用户暂没有商铺。。。");
            }
            return $store;
        } else {
            throw new GQLException("客户端没有登录。。。");
        }
    }

    //根据商铺id查询店铺
    public function getStoresById($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $store = Store::with("product")->where('id', $args['id'])->where("status", 1);
        // dd($store->get());
        return $store->first();
    }
}
