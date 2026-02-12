<?php
/**
 * 单据观察器 Observer
 */

namespace App\Observers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

        // 计算毛利人民币
        Log::info('毛利人民币');
        $grossProfitCny = bcsub($order->receipt_total_cny_amount, $order->payment_total_cny_amount, 2);
        Log::info($grossProfitCny);

        // 计算毛利美金
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
