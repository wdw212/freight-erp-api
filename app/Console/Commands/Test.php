<?php

namespace App\Console\Commands;

use App\Models\OrderFile;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('--测试--');
        $this->info('hello world');
        $orderFiles = OrderFile::query()->get();

        foreach ($orderFiles as $orderFile) {
            $this->info('文件:'.$orderFile->file);
        }
    }
}
