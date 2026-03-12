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
        if (!empty($orderFile->size)) {
            $orderFile->size = (int)$orderFile->size;
            return;
        }

        $filePath = trim((string)($orderFile->file ?? ''));
        if ($filePath === '') {
            $orderFile->size = 0;
            return;
        }

        try {
            $orderFile->size = (int)Storage::fileSize($filePath);
        } catch (\Throwable $e) {
            // 某些存储驱动无法稳定返回文件大小，兜底为 0，避免单据保存失败。
            $orderFile->size = 0;
        }
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
