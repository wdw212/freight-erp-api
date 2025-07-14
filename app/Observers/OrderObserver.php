<?php

namespace App\Observers;

use App\Models\Order;
use Carbon\Carbon;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "saving" event.
     */
    public function saving(Order $order): void
    {
        // 统计单据应付款人民币 和 美金
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
