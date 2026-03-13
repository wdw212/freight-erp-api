<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class SystemInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '系统安装';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (app()->isProduction()) {
            $this->error('生产环境禁止执行 system:install，该命令包含 migrate:fresh。');
            return SymfonyCommand::FAILURE;
        }

        $this->info('--系统数据初始化开始--');
        $this->call('migrate');
        $this->call('migrate:fresh');
        $this->call('db:seed');
        $this->call('storage:link');
        $this->info('--系统数据初始化完成--');
        return SymfonyCommand::SUCCESS;
    }
}
