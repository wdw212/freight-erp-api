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
            ->with('containerType')
            ->groupBy('container_type_id')
            ->selectRaw('container_type_id, count(*) as count')
            ->get()
            ->map(function ($item) {
                return [
                    'type_id' => $item->container_type_id,
                    'type_name' => $item->containerType->name ?? null,
                    'count' => $item->count,
                ];
            });

        $containerType = '';
        foreach ($containerTypeStats as $containerTypeStat) {
            if (empty($containerTypeStat->type_name)) {
                continue;
            }
            $containerType = $containerTypeStat['count'] . '*' . $containerTypeStat['type_name'] . ';';
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
