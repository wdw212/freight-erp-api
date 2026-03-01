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
        // 港口快照：仅在港口ID变化或快照为空时刷新，避免历史数据被主数据改名“追改”
        $originHarborId = empty($order->origin_harbor_id) ? null : (int)$order->origin_harbor_id;
        if (empty($originHarborId)) {
            $order->origin_harbor = null;
        } else if ($order->isDirty('origin_harbor_id') || empty($order->origin_harbor)) {
            $order->origin_harbor = $order->originHarbor?->name;
        }

        $destinationHarborId = empty($order->destination_harbor_id) ? null : (int)$order->destination_harbor_id;
        if (empty($destinationHarborId)) {
            $order->destination_harbor = null;
        } else if ($order->isDirty('destination_harbor_id') || empty($order->destination_harbor)) {
            $order->destination_harbor = $order->destinationHarbor?->name;
        }

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
