<?php

namespace App\Console\Commands;

use App\Models\Harbor;
use Illuminate\Console\Command;

class InitData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:init-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('执行成功');
        Harbor::query()->truncate();
    }
}
