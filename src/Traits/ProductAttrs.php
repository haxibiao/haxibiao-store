<?php

namespace Haxibiao\Store\Traits;

use App\PlatformAccount;

trait ProductAttrs
{
    public function getCoverAttribute()
    {
        // dd($this->image()->first());
        return !is_null($this->image()->first()) ? $this->image()->first() : null;
    }

    public function getAvailableAmountAttribute()
    {
        // dd($this->image()->first());
        //FIXME:商品到上架数量available_amount字段很容易脏，先这样取。。。
        return $this->platformAccount()
            ->where("order_status", PlatformAccount::UNUSE)
            ->whereNotNull("dimension")
            ->whereNotNull("dimension2")
            ->count();
    }
}
