<?php

namespace Haxibiao\Store\Notifications;

use Haxibiao\Breeze\Notifications\BreezeNotification;
use Illuminate\Bus\Queueable;

class OffWorkTechnicianRoom extends BreezeNotification
{
    use Queueable;
    public static $notify_event = "下钟通知";
    protected $user;
    protected $order;

    public function __construct($user, $order)
    {
        $this->user  = $user;
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $order = $this->order;
        //文本描述
        $message = "订单号【{$this->order->id}】到钟通知!";

        $data = [
            'type'  => $order->getMorphClass(),
            'id'    => $order->id,
            'title' => "到钟通知", //标题
            'message' => $message, //通知主体内容
        ];

        return $data;
    }
}
