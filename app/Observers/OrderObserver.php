<?php
/**
 * 单据观察器 Observer
 */

namespace App\Observers;

use App\Models\Order;
use Carbon\Carbon;

class OrderObserver
{
    /**
     * Handle the Order "saving" event.
     */
    public function saving(Order $order): void
    {
        if ((int)$order->payment_status === 0) {
            $order->finish_at = null;
        } else if (empty($order->finish_at)) {
            $order->finish_at = Carbon::now();
        }
        $order->origin_harbor = $order->originHarbor;
        $order->destination_harbor = $order->destinationHarbor;
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        // 单据删除 - 删除关联单据应付款
        $order->orderPayments()->delete();
    }
}
