<?php

namespace Haxibiao\Store\Traits;

use App\Order;
use Carbon\Carbon;

trait OrderAttrs
{
    public function getPasswordAttribute()
    {
        return !is_null($this->platformAccount()->first()) ? $this->platformAccount()->first()->password : null;
    }
    public function getAccountAttribute()
    {
        return !is_null($this->platformAccount()->first()) ? $this->platformAccount()->first()->account : null;
    }

    public function getEndTimeAttribute()
    {
        if ($this->status == Order::PAID) {
            if ($this->platformAccount[0]) {

                $dimension2 = $this->platformAccount[0]->dimension2;
                $end_time   = Carbon::parse($this->created_at)->addHours($dimension2);
                $seconds    = $end_time->diffInSeconds(now());

                return gmstrftime('%H:%M:%S', $seconds);
            }
        }
        return;
    }
}
