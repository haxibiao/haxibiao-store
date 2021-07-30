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

    protected $guarded = [

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

    //定位功能
    public function locations()
    {
        return $this->morphMany(Location::class, 'located');
    }

    public function getLocationAttribute()
    {
        return $this->locations->last();
    }

    public function getLogoAttribute()
    {

        $avatar = $this->getRawOriginal('avatar');
        if (empty($avatar)) {
            //给个默认图片
            return null;
        }
        return cdnurl($avatar);
    }
}
