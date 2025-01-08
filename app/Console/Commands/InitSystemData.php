<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InitSystemData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:init-system-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '初始化系统数据';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('--系统数据初始化开始--');
        $this->call('migrate:fresh');
        $this->call('db:seed');
        $this->call('storage:link');
        $this->info('--系统数据初始化完成--');
    }
}
