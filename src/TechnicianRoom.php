<?php

namespace Haxibiao\Store;

use App\TechnicianProfile;
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
        $status   = $args['status'] ?? null;
        $store_id = $args['store_id'] ?? null;
        return TechnicianRoom::query()->when($status, function ($q) use ($status) {
            return $q->where('status', $status);
        })->when($store_id, function ($q) use ($store_id) {
            return $q->where('store_id', $store_id);
        });
    }

    //根据状态查询订单列表
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
        $room_id       = $args['room_id'] ?? null;
        $product_id    = $args['product_id'] ?? null;
        $order_id      = $args['order_id'] ?? null;
        $technician_id = $args['technician_id'] ?? null;
        //保存房间信息
        $technicianRoom = TechnicianRoom::find($room_id);
        throw_if(empty($technicianRoom), GQLException::class, "没有该房间");
        $technician_user = User::find($technician_id);
        throw_if(empty($technician_user), GQLException::class, "没有该技师");
        $technicianProfile = $technician_user->technicianProfile;
        if ($technicianProfile->status == TechnicianProfile::WORK_STATUS) {
            throw new GQLException("该技师正在服务中，换一个技师吧！");
        } else if ($technicianProfile->status == TechnicianProfile::NOT_WORK_STATUS) {
            throw new GQLException("该技师休息中，换一个技师吧！");
        }

        //关联订单信息
        if ($order_id) {
            $order = Order::find($order_id);
            throw_if(empty($order), GQLException::class, "没有该订单");
            $order->product_id         = $product_id;
            $order->technician_id      = $technician_id;
            $order->technician_room_id = $room_id;
            $order->status             = Order::ACCEPT;
            $order->save();
        } else {
            //没有预约的话当场创建订单
            $order = Order::create([
                "user_id"          => 1, //匿名用户
                "store_id" => $technician_user->store_id,
                "technician_id"    => $technician_id,
                "appointment_time" => now(),
                "number"           => str_random(8) . time(),
                "status"           => Order::ACCEPT,
            ]);
        }

        //修改房间信息
        $uids                   = array_unique(array_merge([$technician_id], $technicianRoom->uids ?? []));
        $technicianRoom->uids   = $uids;
        $technicianRoom->status = AppTechnicianRoom::SERVICE_STATUS;
        $technicianRoom->save();

        //修改技师状态
        $technicianProfile->update(['status' => TechnicianProfile::WORK_STATUS]);
        return $technicianRoom;
    }

    //开始上钟，计时开始
    public function resolveAtWorkTechnicianRoom($rootValue, $args, $context, $resolveInfo)
    {
        $order = Order::find($args['order_id']);
        throw_if(empty($order), GQLException::class, "没有该订单");
        throw_if(empty($technicianUser), GQLException::class, "订单错误");

        //保存上钟时间
        if ($order->status != Order::WORKING) {
            $order->at_work_time = now();
            $order->status       = Order::WORKING;
            $order->save();
        }

        return $order;
    }
}
