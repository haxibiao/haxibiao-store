<?php

namespace haxibiao\store;

use App\User;
use App\Product;
use App\PlatformAccount;
use haxibiao\store\Traits\OrderRepo;
use haxibiao\store\Traits\OrderAttrs;
use Illuminate\Database\Eloquent\Model;
use haxibiao\store\Traits\OrderResolvers;

class Order extends Model
{
    use OrderResolvers;
    use OrderAttrs;
    use OrderRepo;

    protected $fillable = [
        'user_id',
        'number',
        'status',
        'platformAccount_id',
    ];

    const UNPAY    = 0; //未支付
    const PAID     = 1; //已支付
    const RECEIVED = 2; //已到货
    const EXPIRE   = 3; //已过期

    const REFUND_TIME = 600; //订单可退款时间（s）
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function products()
    {
        return $this->belongsToMany(\App\Product::class, "product_order")
            ->withPivot("amount")
            ->withPivot("price")
            ->withTimestamps();
    }

    public function platformAccount()
    {
        return $this->hasMany(\App\PlatformAccount::class);
    }

    public function platformAccount2()
    {
        return $this->hasOne(\App\PlatformAccount::class, "id", "platformAccount_id");
    }
}
