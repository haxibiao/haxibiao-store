<?php

namespace Haxibiao\Store;

use App\PlatformAccount;
use App\User;
use Haxibiao\Store\Traits\OrderAttrs;
use Haxibiao\Store\Traits\OrderRepo;
use Haxibiao\Store\Traits\OrderResolvers;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use OrderResolvers;
    use OrderAttrs;
    use OrderRepo;

    protected $guarded = [

    ];

    //约钟
    const RESERVE = -1; //已预约
    const REJECT  = -2; //已拒绝
    const CANCEL  = -3; //已取消
    const ACCEPT  = 4; //已接受
    const WORKING = 5; //进行中
    const OVER    = 6; //已结束
    const ALLOT   = 7; //已派钟

    //租号
    const UNPAY    = 0; //未支付
    const PAID     = 1; //已支付
    const RECEIVED = 2; //已到货
    const EXPIRE   = 3; //已过期

    const REFUND_TIME = 600; //订单可退款时间（s）

    protected static function boot()
    {
        parent::boot();
        static::observe(Observers\OrderObserver::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function product()
    {
        return $this->belongsTo(\App\Product::class);
    }

    public function technicianRoom()
    {
        return $this->belongsTo(\App\TechnicianRoom::class);
    }

    public function store()
    {
        return $this->belongsTo(\App\Store::class);
    }

    public function technicianUser()
    {
        return $this->belongsTo(\App\User::class, 'technician_id', 'id');
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
