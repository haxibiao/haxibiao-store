<?php

namespace Haxibiao\Store\Traits;

use App\Store;
use GraphQL\Type\Definition\ResolveInfo;
use Haxibiao\Breeze\Exceptions\GQLException;
use Haxibiao\Content\Location;
use Haxibiao\Media\Image;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

trait StoreResolvers
{

    //创建店铺信息
    public function resolveCreateStore($root, $args, $context, $resolveInfo)
    {
        $args = $args['input'];
        $user = getUser();
        app_track_event("用户", "创建店铺");
        $store = Store::firstOrNew([
            'user_id' => $user->id,
            'name'    => $args['name'],
        ]);
        $location = data_get($args, 'location', null);
        $images   = data_get($args, 'images', null);
        $logo     = data_get($args, 'logo', null);
        $store->fill(array_except($args, ['location', 'images', 'logo']))->save();

        //图片
        if ($images) {
            $imageIds = [];
            foreach ($images as $image) {
                $model      = Image::saveImage($image);
                $imageIds[] = $model->id;
            }
            $store->images()->sync($imageIds);
        }

        //保存LOGO
        if (!blank($logo)) {
            $image = Image::saveImage($logo);
            if (!empty($image)) {
                $store->update(['logo' => $image->path]);
            }
        }

        //地址
        if ($location) {
            Location::storeLocation($location, 'stores', $store->id);
        }
        return $store;

    }

    //根据用户id查询店铺
    public function resolveGetStore($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if ($user = currentUser()) {
            $store = Store::with("product")
                ->where('user_id', $args['user_id'])->publishStatus()->first();
            if (empty($store)) {
                throw new GQLException("该用户暂没有商铺。。。");
            }
            return $store;
        } else {
            throw new GQLException("客户端没有登录。。。");
        }
    }

    //根据用户id查询店铺
    public function resolveGetStores($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        // if ($user = currentUser()) {
        return Store::query()
            ->when($args['name'] ?? null, function ($qb) use ($args) {
                return $qb->where('name', 'like', '%' . $args['name'] . '%');
            })
            ->publishStatus();
        // } else {
        //     throw new GQLException("客户端没有登录。。。");
        // }
    }

    //获取同城店铺
    public function resolveCityStores($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        //目前只搞衡阳的，简单的取出全部店铺
        return Store::query()->publishStatus();
    }

    //获取当前用户位置附近的店铺
    public function resolveNearByStores($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $store_ids = Location::getNearbyStoreIds(getUser(false));
        return Store::query()
            ->publishStatus()
            ->whereIn('id', $store_ids);

    }

    //根据商铺id查询店铺
    public function getStoresById($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $store = Store::with("product")->where('id', $args['id'])->publishStatus();
        // dd($store->get());
        return $store->first();
    }
}
