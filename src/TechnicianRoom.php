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

    protected $guarded = [];

    public function getUsersAttribute()
    {
        return User::whereIn('id', $this->uids)->get();
    }

    public function getTechnicianUsersAttribute()
    {
        return User::whereIn('id', $this->tids)->get();
    }

    //用户状态：参考qq、微信状态
    public function getStatus()
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
}
