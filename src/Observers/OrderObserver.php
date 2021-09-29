<?php

namespace Haxibiao\Store\Observers;

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
        if ($order->status = Order::REJECT) {

            //1.拒绝接单 通知用户
            $order->user->notify(new OrderNotification($order));
        } else if ($order->status = Order::CANCEL) {
            //2.取消订单 通知商家
            $order->store->user->notify(new OrderNotification($order));
        } else if ($order->status = Order::ACCEPT) {
            //3.接单 通知技师和用户
            $order->user->notify(new OrderNotification($order));
            $order->technicianUser->notify(new OrderNotification($order));
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
