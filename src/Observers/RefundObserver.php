<?php

namespace Haxibiao\Store\Observers;

use App\Gold;
use App\Order;
use Exception;
use App\Refund;
use App\PlatformAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefundObserver
{

    public function created(Refund $refund)
    {
    }

    /**
     * Handle the Refund "updated" event.
     *
     * @param  \App\Refund  $refund
     * @return void
     */
    public function updated(Refund $refund)
    {
        $order = Order::find($refund->order_id);

        //后台同意处理退款
        if ($refund->status == Refund::STATUS_AGREE) {
            DB::BeginTransaction();
            try {
                $order->status = Order::EXPIRE;
                $order->save();
                //更改关联账号状态为2（过期）
                $platformAccounts = $order->platformAccount;
                $gold             = 0;
                foreach ($platformAccounts as $platformAccount) {
                    $gold += $platformAccount->price;
                    $platformAccount->order_status = PlatformAccount::EXPIRE;
                    $platformAccount->save();
                }
                //退款给用户
                Gold::makeIncome($order->user, $gold, "退款返回");
                $refund->update(["status" => Refund::STATUS_PROCESSED]);
                DB::commit();
            } catch (Exception $e) {
                Log::error("发生未知错误，退款失败！");
                DB::rollback();
            }
        }
        //
    }

    /**
     * Handle the Refund "deleted" event.
     *
     * @param  \App\Refund  $refund
     * @return void
     */
    public function deleted(Refund $refund)
    {
        //同步用户关注数
        $user                            = $refund->user;
        $user->profile->count_Refundings = $user->Refundings()->count();
        $user->profile->save();

        //同步被关注着的粉丝数
        $count = Refund::where('Refunded_type', $refund->Refunded_type)->where("Refunded_id", $refund->Refunded_id)->count();
        if ($refund->Refunded_type == 'users') {
            $refund->Refunded->profile->update(['count_Refunds' => $count]);
        } else {
            $refund->Refunded->update(['count_Refunds' => $count]);
        }
    }

    /**
     * Handle the Refund "restored" event.
     *
     * @param  \App\Refund  $refund
     * @return void
     */
    public function restored(Refund $refund)
    {
        //
    }

    /**
     * Handle the Refund "force deleted" event.
     *
     * @param  \App\Refund  $refund
     * @return void
     */
    public function forceDeleted(Refund $refund)
    {
        //
    }
}
