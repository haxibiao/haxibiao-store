<?php

namespace Haxibiao\Store\Observers;

use Haxibiao\Breeze\Events\OrderBroadcast;
use Haxibiao\Breeze\Notifications\OrderNotification;
use Haxibiao\Store\Order;
use Haxibiao\Store\TechnicianProfile;
use Haxibiao\Store\TechnicianRoom;

class OrderObserver
{

    public function created(Order $order)
    {
        //订单创建 通知商家
        if ($store = $order->store) {
            if ($user = $store->user) {
                $user->notify(new OrderNotification($order));
                event(new OrderBroadcast($order, $user->id));
            }
        }
    }

    /**
     * Handle the Refund "updated" event.
     *
     * @param  \App\Refund  $refund
     * @return void
     */
    public function updated(Order $order)
    {
        //状态发生更新
        if (!is_null($order->getChanges()['status'] ?? null)) {
            if ($order->status == Order::REJECT) {
                //1.拒绝接单 通知用户
                $user = $order->user;
                $user->notify(new OrderNotification($order));
                event(new OrderBroadcast($order, $user->id));
            } else if ($order->status == Order::CANCEL) {
                //2.取消订单 通知商家
                $order->store->user->notify(new OrderNotification($order));
                event(new OrderBroadcast($order, $order->store->user->id));
            } else if ($order->status == Order::ACCEPT) {
                //3.接单 通知技师和用户
                $order->user->notify(new OrderNotification($order));
                $order->technicianUser->notify(new OrderNotification($order));
                event(new OrderBroadcast($order, $order->user->id));
                event(new OrderBroadcast($order, $order->technicianUser->id));
            } else if ($order->status == Order::OVER) {
                //4.完成订单 修改技师状态
                $technicianUser    = $order->technicianUser;
                $technicianProfile = $technicianUser->technicianProfile;
                //技师状态修改为空闲
                if ($technicianProfile && $technicianProfile->status != TechnicianProfile::FREE_STATUS) {
                    $technicianProfile->update(['status' => TechnicianProfile::FREE_STATUS]);
                }
                //房间状态修改
                $room = $order->technicianRoom;
                if ($room && count($room->uids)) {
                    //技师移出
                    $uids = array_values(array_diff($room->uids, [$technicianUser->id]));
                    //状态修改
                    if (count($uids) == 0) {
                        $room->status = TechnicianRoom::FREE_STATUS;
                    }
                    $room->uids = $uids;
                    $room->save();
                }
            }
        }
    }

    /**
     * Handle the Refund "deleted" event.
     *
     * @param  \App\Refund  $refund
     * @return void
     */
    public function deleted(Order $order)
    {

    }

    /**
     * Handle the Refund "restored" event.
     *
     * @param  \App\Refund  $refund
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the Refund "force deleted" event.
     *
     * @param  \App\Refund  $refund
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }
}
