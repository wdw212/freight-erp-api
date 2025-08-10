<?php

namespace App\Observers;

use App\Models\OrderPayment;

class OrderPaymentObserver
{
    /**
     * Handle the OrderPayment "created" event.
     */
    public function created(OrderPayment $orderPayment): void
    {
        //
    }

    /**
     * Handle the OrderPayment "updated" event.
     */
    public function updated(OrderPayment $orderPayment): void
    {
        //
    }

    /**
     * Handle the OrderPayment "deleted" event.
     */
    public function deleted(OrderPayment $orderPayment): void
    {
        //
    }

    /**
     * Handle the OrderPayment "restored" event.
     */
    public function restored(OrderPayment $orderPayment): void
    {
        //
    }

    /**
     * Handle the OrderPayment "force deleted" event.
     */
    public function forceDeleted(OrderPayment $orderPayment): void
    {
        //
    }
}
