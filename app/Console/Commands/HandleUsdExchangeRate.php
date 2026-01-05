<?php

namespace App\Console\Commands;

use App\Models\UsdExchangeRate;
use Carbon\Carbon;
use Illuminate\Console\Command;

class HandleUsdExchangeRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:handle-usd-exchange-rate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每月生成美元汇率';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $monthCode = Carbon::now()->format('Y-m');
        $usdExchangeRate = UsdExchangeRate::query()->where('month_code', $monthCode)->first();
        if (!$usdExchangeRate) {
            $lastUsdExchangeRate = UsdExchangeRate::query()->latest()->first();
            $usdExchangeRate = new UsdExchangeRate();
            $usdExchangeRate->month_code = $monthCode;
            $usdExchangeRate->exchange_rate = $lastUsdExchangeRate->exchange_rate;
            $usdExchangeRate->save();
        }
    }
}
