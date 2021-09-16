<?php

namespace Haxibiao\Store;

use App\Category;
use App\Store;
use App\User;
use App\Video;
use Haxibiao\Breeze\Traits\HasFactory;
use Haxibiao\Media\Traits\Imageable;
use Haxibiao\Store\Traits\ProductAttrs;
use Haxibiao\Store\Traits\ProductRepo;
use Haxibiao\Store\Traits\ProductResolvers;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    use ProductResolvers;
    use ProductAttrs;
    use ProductRepo;
    use Imageable;

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

    public static function boot()
    {
        parent::boot();
        //保存时触发
        self::saving(function ($model) {
            if (empty($model->user_id)) {
                $model->user_id = $model->store->user_id;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function technicianUsers()
    {
        return $this->belongsToMany(\App\User::class, 'technician_products');
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

    /**
     * 租号系统早期简单商品用图的关系，
     * //TODO: 建议重构用Imageable特性的images关系
     */
    public function image()
    {
        return $this->hasMany(\App\Image::class);
    }
}
