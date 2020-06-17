<?php

namespace haxibiao\store;

use App\User;

use App\Image;
use App\Product;
use haxibiao\store\Traits\StoreRepo;
use Illuminate\Database\Eloquent\Model;
use haxibiao\store\Traits\StoreResolvers;

class Store extends Model
{
    use StoreResolvers;
    use StoreRepo;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function product()
    {
        return $this->hasMany(\App\Product::class);
    }

    public function image()
    {
        return $this->hasMany(\App\Image::class);
    }
}
