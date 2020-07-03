<?php

namespace Haxibiao\Store\Traits;

use App\Exceptions\GQLException;
use App\Gold;
use App\Item;

trait ItemRepo
{
    //领取道具
    public static function receiveItem($user, $item_id)
    {
        if ($user) {

            $item = Item::find($item_id);
            if ($item) {
                if ($user->items()->find($item_id)) {
                    //TODO:已经存在该道具，数量应该叠加
                    return false;
                }

                if ($item->price > 0) {
                    if ($item->price > $user->gold) {
                        throw new GQLException("金币不足，购买失败！");
                    }
                    Gold::makeOutcome($user, $item->value, "购买道具扣除");
                }

                $user->items()->syncWithoutDetaching([
                    $item->id => [
                        "total" => 1,
                    ],
                ]);
                return true;
            }
            throw new GQLException("不存在该物品！");
        }
        return false;
    }

    //我的道具
    public static function myItems()
    {
        if ($user = checkUser()) {
            return $user->items()->get();
        }
    }
}
