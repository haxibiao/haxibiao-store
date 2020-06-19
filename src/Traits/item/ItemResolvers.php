<?php

namespace haxibiao\store\Traits;

use App\Item;

trait ItemResolvers
{

    /**
     * 领取道具
     */
    public function resolveReceiveItem($root, array $args, $context, $info)
    {
        return Item::receiveItem(getUser(), $args['id']);
    }

    /**
     * 我的道具
     */
    public function resolveMyItems($root, array $args, $context, $info)
    {
        return Item::myItems();
    }
}
