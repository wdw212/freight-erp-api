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
        $containerTypeStats = Container::query()
            ->where('order_id', $container->order->id)
            ->whereNotNull('container_type_name')
            ->where('container_type_name', '<>', '')
            ->groupBy('container_type_name')
            ->selectRaw('container_type_name as type_name, count(*) as count')
            ->get();
        $containerType = '';
        foreach ($containerTypeStats as $containerTypeStat) {
            if (empty($containerTypeStat['type_name'])) {
                continue;
            }
            $containerType .= $containerTypeStat['count'] . '*' . $containerTypeStat['type_name'] . ';';
        }
        $container->order->container_type = $containerType;
        $container->order->save();
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
