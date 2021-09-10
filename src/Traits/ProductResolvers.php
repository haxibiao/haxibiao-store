<?php

namespace Haxibiao\Store\Traits;

use App\Product;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

trait ProductResolvers
{
    public function getProducts($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $store_id = $args['store_id'] ?? null;
        return Product::query()->where("status", 1)->when($store_id, function ($qb) use ($store_id) {
            return $qb->where('store_id', $store_id);
        });
    }
}
