<?php

namespace Haxibiao\Store;

use App\User;
use Haxibiao\Store\Traits\ItemRepo;
use Haxibiao\Store\Traits\ItemResolvers;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use ItemResolvers;
    use ItemRepo;

    protected $fillable = [
        'price',
        'count',
        'value',
        'name',
        'description',
        'status',
    ];

    public function users()
    {
        return $this->belongsToMany(\App\User::class);
    }
}
