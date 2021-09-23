<?php

namespace Haxibiao\Store;

use Haxibiao\Breeze\Model;
use Haxibiao\Breeze\User;

class TechnicianRoom extends Model
{
    //空闲
    const FREE_STATUS = 0;
    //工作中
    const SERVICE_STATUS = 1;

    protected $casts = [
        'uid' => 'array',
    ];

    protected $guarded = [];

    public function getUsersAttribute()
    {
        if ($this->uids) {
            return User::whereIn('id', $this->uids)->get();
        }
        return null;
    }

    public function getTechnicianUsersAttribute()
    {
        if ($this->tids) {
            return User::whereIn('id', $this->tids)->get();
        }
    }

    //用户状态：参考qq、微信状态
    public static function getStatus()
    {
        return [
            self::FREE_STATUS    => "空闲中",
            self::SERVICE_STATUS => "忙碌中",
        ];
    }

    //根据状态查询房间列表
    public function resolveTehnicianRooms($rootValue, $args, $context, $resolveInfo)
    {
        $status = $args['status'] ?? null;
        return TechnicianRoom::query()->when($status, function ($q) use ($status) {
            return $q->where('status', $status);
        });
    }

    //派钟
    public function resolveAllotTechnicianRoom($rootValue, $args, $context, $resolveInfo)
    {
        $uids           = $args['uids'] ?? null;
        $id             = $args['id'] ?? null;
        $technicianRoom = TechnicianRoom::findOrFail($id);

        $uids                 = array_unique(array_merge($uids, $technicianRoom->uids ?? []));
        $technicianRoom->uids = $uids;
        $technicianRoom->save();
        return $technicianRoom;
    }
}
