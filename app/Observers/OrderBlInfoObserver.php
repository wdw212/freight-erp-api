<?php

namespace App\Observers;

use App\Models\OrderBlInfo;

class OrderBlInfoObserver
{
    /**
     * Handle the OrderBlInfo "created" event.
     */
    public function created(OrderBlInfo $orderBlInfo): void
    {
        //
    }

    /**
     * Handle the OrderBlInfo "updated" event.
     */
    public function updated(OrderBlInfo $orderBlInfo): void
    {
        //
    }

    /**
     * Handle the OrderBlInfo "deleted" event.
     */
    public function deleted(OrderBlInfo $orderBlInfo): void
    {
        //
    }

    /**
     * Handle the OrderBlInfo "restored" event.
     */
    public function restored(OrderBlInfo $orderBlInfo): void
    {
        //
    }

    /**
     * Handle the OrderBlInfo "force deleted" event.
     */
    public function forceDeleted(OrderBlInfo $orderBlInfo): void
    {
        //
    }
}
