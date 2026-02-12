<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('--开始--');
        $orders = Order::all();
        foreach ($orders as $order) {
            $order->save();
            $this->info('订单执行完成:' . $order->id);
        }
        $invoices = Invoice::all();
        foreach ($invoices as $invoice) {
            $invoice->save();
            $this->info('发票处理完成:' . $invoice->id);
        }
        $this->info('--完成--');
    }
}
