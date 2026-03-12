<?php

namespace App\Observers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    private const SNAPSHOT_JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    /**
     * Handle the Order "saving" event.
     */
    public function saving(Order $order): void
    {
        if ((int)$order->payment_status === 0) {
            $order->finish_at = null;
        } elseif (empty($order->finish_at)) {
            $order->finish_at = Carbon::now();
        }

        $originHarborId = empty($order->origin_harbor_id) ? null : (int)$order->origin_harbor_id;
        if (empty($originHarborId)) {
            $order->origin_harbor = null;
        } elseif ($order->isDirty('origin_harbor_id') || empty($order->origin_harbor)) {
            $order->origin_harbor = $this->encodeHarborSnapshotName($order->originHarbor?->name);
        }

        $destinationHarborId = empty($order->destination_harbor_id) ? null : (int)$order->destination_harbor_id;
        if (empty($destinationHarborId)) {
            $order->destination_harbor = null;
        } elseif ($order->isDirty('destination_harbor_id') || empty($order->destination_harbor)) {
            $order->destination_harbor = $this->encodeHarborSnapshotName($order->destinationHarbor?->name);
        }

        Log::info('毛利人民币');
        $grossProfitCny = bcsub($order->receipt_total_cny_amount, $order->payment_total_cny_amount, 2);
        $grossProfitCny = bcsub($grossProfitCny, $order->special_fee, 2);

        Log::info($grossProfitCny);
        $grossProfitUsd = bcmul(
            bcsub($order->receipt_total_usd_amount, $order->payment_total_usd_amount, 2),
            $order->usd_exchange_rate,
            2
        );
        Log::info('毛利美金');
        Log::info($grossProfitUsd);
        $totalProfit = bcsub($grossProfitCny, $order->special_fee, 2);
        Log::info($totalProfit);
        $order->total_profit = $totalProfit;
        $order->gross_profit_cny = $grossProfitCny;
        $order->gross_profit_usd = $grossProfitUsd;
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        $order->orderPayments()->delete();
    }

    private function encodeHarborSnapshotName(?string $name): ?string
    {
        $trimmedName = trim((string)($name ?? ''));
        if ($trimmedName === '') {
            return null;
        }

        return json_encode($trimmedName, self::SNAPSHOT_JSON_FLAGS);
    }
}
