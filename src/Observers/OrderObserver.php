<?php

namespace Haxibiao\Store\Observers;

use Haxibiao\Breeze\Events\OrderBroadcast;
use Haxibiao\Breeze\Notifications\OrderNotification;
use Haxibiao\Store\Order;

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
                \info("拒绝接单");
                //1.拒绝接单 通知用户
                $user = $order->user;
                $user->notify(new OrderNotification($order));
                event(new OrderBroadcast($order, $user->id));
            } else if ($order->status == Order::CANCEL || $order->status == Order::OVER) {
                //2.取消订单 通知商家
                //完成订单 修改技师状态
                Order::overTechnicianRoomOrder($order);
                $order->store->user->notify(new OrderNotification($order));
                event(new OrderBroadcast($order, $order->store->user->id));
            } else if ($order->status == Order::ACCEPT) {
                \info("接单");
                //3.接单 通知技师和用户
                $order->user->notify(new OrderNotification($order));
                $order->technicianUser->notify(new OrderNotification($order));
                event(new OrderBroadcast($order, $order->user->id));
                event(new OrderBroadcast($order, $order->technicianUser->id));
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
