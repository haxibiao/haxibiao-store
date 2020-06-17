<?php

namespace haxibiao\store;

use App\User;
use haxibiao\store\Traits\ItemRepo;
use Illuminate\Database\Eloquent\Model;
use haxibiao\store\Traits\ItemResolvers;

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
