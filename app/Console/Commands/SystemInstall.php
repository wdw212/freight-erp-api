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
        $this->error('system:install 已禁用，该命令包含 migrate:fresh，禁止在任何环境直接执行。');
        return SymfonyCommand::FAILURE;
    }
}
