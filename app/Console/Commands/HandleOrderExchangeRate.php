<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\UsdExchangeRate;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class HandleOrderExchangeRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:handle-order-exchange-rate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '处理订单汇率';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Log::info('未结算的订单 - 修改当月汇率');
        // 未结算的订单 - 修改当月汇率
        $monthCode = Carbon::now()->format('Y-m');
        $currentUsdExchangeRate = UsdExchangeRate::query()->where('month_code', $monthCode)->first();
        Order::query()->where('is_finish', 0)->update([
            'usd_exchange_rate' => $currentUsdExchangeRate->exchange_rate,
        ]);
    }
}
