<?php

namespace Haxibiao\Store;

use App\Image;
use App\Location;
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

    public function getMorphClass()
    {
        return 'stores';
    }

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
        return $this->morphMany(\App\Location::class, 'located');
    }

    public function getLocationAttribute()
    {
        return $this->locations->last();
    }

    public function getLogoAttribute()
    {

        $logo = $this->getRawOriginal('logo');
        if (empty($logo)) {
            //给个默认图片
            return null;
        }
        return cdnurl($logo);
    }

    public function getDistanceAttribute()
    {
        if ($user = currentUser()) {
            if (!empty($user->location) && !empty($this->location)) {
                $longitude1 = $user->location->longitude;
                $latitude1  = $user->location->latitude;
                $longitude2 = $this->location->longitude;
                $latitude2  = $this->location->latitude;
                if ($longitude1 && $latitude1 && $longitude2 && $latitude2) {
                    $distance = Location::getDistance($longitude1, $latitude1, $longitude2, $latitude2, 1);
                    return numberToReadable($distance) . 'm';
                }
            }
        } else {
            return null;
        }

    }
}
