<?php

namespace App\Observers;

use App\Models\Container;
use Illuminate\Support\Facades\Log;

class ContainerObserver
{
    /**
     * Handle the Container "saved" event.
     */
    public function saved(Container $container): void
    {
        Log::info('--集装箱保存完成--');

        // 更新订单 柜子类型
    }

    /**
     * Handle the Container "updated" event.
     */
    public function updated(Container $container): void
    {
        //
    }

    /**
     * Handle the Container "deleted" event.
     */
    public function deleted(Container $container): void
    {
        //
    }
}
