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
        $grossProfitCny = bcsub($grossProfitCny, $order->special_fee, 2);

        Log::info($grossProfitCny);
        $grossProfitUsd = bcmul(bcsub($order->receipt_total_usd_amount, $order->payment_total_usd_amount, 2), $order->usd_exchange_rate, 2);
        Log::info('毛利美金');
        Log::info($grossProfitUsd);
        $totalProfit = bcsub($grossProfitCny, $order->special_fee, 2);
        Log::info($totalProfit);
        $order->total_profit = $totalProfit;
        $order->gross_profit_cny = $grossProfitCny;
        $order->gross_profit_usd = $grossProfitUsd;
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
