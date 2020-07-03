<?php

namespace Haxibiao\Store;

use App\Category;
use App\Store;
use App\User;
use App\Video;
use Haxibiao\Store\Traits\ProductAttrs;
use Haxibiao\Store\Traits\ProductRepo;
use Haxibiao\Store\Traits\ProductResolvers;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use ProductResolvers;
    use ProductAttrs;
    use ProductRepo;

    protected $fillable = [
        'name',
        'parent_id',
        'store_id',
        'price',
        'dimension',
        'dimension2',
        'available_amount',
        'amount',
        'description',
        'user_id',
        'status',
        'category_id',
        'video_id',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function store()
    {
        return $this->belongsTo(\App\Store::class);
    }

    public function category()
    {
        return $this->belongsTo(\App\Category::class);
    }

    public function parent()
    {
        return $this->belongsTo(\App\Product::class);
    }

    public function video()
    {
        return $this->belongsTo(\App\Video::class);
    }

    public function platformAccount()
    {
        return $this->hasMany(\App\PlatformAccount::class);
    }

    public function orders()
    {
        return $this->belongsToMany(\App\Order::class, "product_order")
            ->withPivot("amount")
            ->withTimestamps();
    }

    public function image()
    {
        return $this->hasMany(\App\Image::class);
    }
}
