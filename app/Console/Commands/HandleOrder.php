<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class HandleOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:handle-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '--处理单据--';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Todo: 处理超期订单
        $orders = Order::query()->where('is_delivery', 0)->get();
        foreach ($orders as $order) {
            if (Carbon::parse($order->actual_arrival_at)->addDays(7) < Carbon::now()) {
                $order->update([
                    'is_delivery' => 2,
                ]);
            }
        }
    }
}
