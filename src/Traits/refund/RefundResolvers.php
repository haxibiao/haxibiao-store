<?php

namespace Haxibiao\Store\Traits;

use App\Image;
use Haxibiao\Store\Refund;
use App\Exceptions\GQLException;
use Haxibiao\Helpers\BadWordUtils;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

trait RefundResolvers
{
    public function resolveCreateRefund($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        app_track_event('用户页', '订单退款');

        $user    = getUser();
        $contact = array_get($args, "contact");
        $order_id = array_get($args, "order_id");
        if (BadWordUtils::check($args['content'])) {
            throw new GQLException('退款理由中含有包含非法内容,请删除后再试!');
        }
        $refund = Refund::firstOrCreate([
            'user_id' => $user->id,
            'order_id' => $order_id,
            'contact' => $contact,
        ]);

        if (!empty($args['images'])) {
            foreach ($args['images'] as $image) {
                $image = Image::saveImage($image);
                $refund->images()->attach($image->id);
            }
        }

        if (!empty($args['image_urls']) && is_array($args['image_urls'])) {
            $image_ids = array_map(function ($url) {
                return intval(pathinfo($url)['filename']);
            }, $args['image_urls']);
            $refund->images()->sync($image_ids);
        }
        $refund->save();

        return $refund;
    }
}
