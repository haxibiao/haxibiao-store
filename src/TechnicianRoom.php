<?php

namespace Haxibiao\Store;

use App\TechnicianRoom as AppTechnicianRoom;
use Haxibiao\Breeze\Exceptions\GQLException;
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

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getUsersAttribute()
    {
        if ($this->uids) {
            return User::whereIn('id', $this->uids)->get();
        }
        return null;
    }

    public function getUidsAttribute($value)
    {
        return json_decode($value);
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

    //根据状态查询房间列表
    public function resolveOrders($rootValue, $args, $context, $resolveInfo)
    {
        $status = $args['status'] ?? null;
        return $rootValue->orders()->when($status, function ($q) use ($status) {
            return $q->where('status', $status);
        })->orderByDesc('id');
    }

    //派钟
    public function resolveAllotTechnicianRoom($rootValue, $args, $context, $resolveInfo)
    {
        $room_id    = $args['room_id'] ?? null;
        $product_id = $args['product_id'] ?? null;
        $order_id   = $args['order_id'] ?? null;

        //关联订单信息
        $order = Order::find($order_id);
        throw_if(empty($order), GQLException::class, "没有改订单");
        $order->product_id         = $product_id;
        $order->technician_room_id = $room_id;
        $order->status             = Order::WORKING;
        $order->save();

        //保存房间信息
        $technicianRoom = TechnicianRoom::find($room_id);
        throw_if(empty($order), GQLException::class, "没有改房间");
        $technician_id          = $order->technician_id;
        $uids                   = array_unique(array_merge([$technician_id], $technicianRoom->uids ?? []));
        $technicianRoom->uids   = $uids;
        $technicianRoom->status = AppTechnicianRoom::SERVICE_STATUS;
        $technicianRoom->save();
        return $technicianRoom;
    }
}
