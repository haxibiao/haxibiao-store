<?php

namespace Haxibiao\Store;

use App\Image;
use App\Product;
use App\User;
use Haxibiao\Breeze\Traits\HasFactory;
use Haxibiao\Store\Traits\StoreRepo;
use Haxibiao\Store\Traits\StoreResolvers;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
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
