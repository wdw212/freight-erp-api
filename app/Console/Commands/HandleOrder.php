<?php

namespace App\Console\Commands;

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
        Log::info('--处理单据--');
    }
}
