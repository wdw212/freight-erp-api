<?php

namespace App\Observers;

use App\Models\OrderFile;
use Illuminate\Support\Facades\Storage;

class OrderFileObserver
{
    /**
     * Handle the OrderFile "created" event.
     */
    public function created(OrderFile $orderFile): void
    {
        //
    }

    /**
     * Handle the OrderFile "updated" event.
     */
    public function saving(OrderFile $orderFile): void
    {
        $size = Storage::fileSize($orderFile->file);
        $orderFile->size = $size;
    }

    /**
     * Handle the OrderFile "deleted" event.
     */
    public function deleted(OrderFile $orderFile): void
    {
        //
    }

    /**
     * Handle the OrderFile "restored" event.
     */
    public function restored(OrderFile $orderFile): void
    {
        //
    }

    /**
     * Handle the OrderFile "force deleted" event.
     */
    public function forceDeleted(OrderFile $orderFile): void
    {
        //
    }
}
