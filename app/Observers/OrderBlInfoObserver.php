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
     * Handle the OrderBlInfo "saved" event.
     */
    public function saving(OrderBlInfo $orderBlInfo): void
    {
        if ($orderBlInfo->sender_id === '') {
            $orderBlInfo->sender_id = null; // 或 auth()->id()
        } elseif (!is_null($orderBlInfo->sender_id) && !is_numeric($orderBlInfo->sender_id)) {
            $orderBlInfo->sender_id= null;
        } elseif (is_numeric($orderBlInfo->sender_id)) {
            // 确保是整数类型
            $orderBlInfo->sender_id = (int)$orderBlInfo->sender_id;
        }

        if ($orderBlInfo->receiver_id === '') {
            $orderBlInfo->receiver_id = null; // 或 auth()->id()
        } elseif (!is_null($orderBlInfo->receiver_id) && !is_numeric($orderBlInfo->receiver_id)) {
            $orderBlInfo->receiver_id= null;
        } elseif (is_numeric($orderBlInfo->receiver_id)) {
            // 确保是整数类型
            $orderBlInfo->receiver_id = (int)$orderBlInfo->receiver_id;
        }

        if ($orderBlInfo->notifier_id === '') {
            $orderBlInfo->notifier_id = null;
        } elseif (!is_null($orderBlInfo->notifier_id) && !is_numeric($orderBlInfo->notifier_id)) {
            $orderBlInfo->notifier_id= null;
        } elseif (is_numeric($orderBlInfo->notifier_id)) {
            // 确保是整数类型
            $orderBlInfo->notifier_id = (int)$orderBlInfo->notifier_id;
        }
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
