<?php

namespace Haxibiao\Store;

use Illuminate\Database\Eloquent\Model;
use Haxibiao\Store\Traits\RefundResolvers;

class Refund extends Model
{
    use RefundResolvers;
    protected $fillable = [
        'order_id',
        'user_id',
        'content',
        'status',
        'created_at',
        'updated_at'
    ];

    // 待处理
    const STATUS_PENDING = 0;

    //同意申请
    const STATUS_AGREE = 1;

    // 已处理
    const STATUS_PROCESSED = 2;
    public function order()
    {
        $this->hasOne(\App\Order::class);
    }

    public function user()
    {
        $this->hasOne(\App\User::class);
    }

    public function images()
    {
        return $this->belongsToMany(\App\Image::class, "refund_image")->withTimestamps();
    }
}
