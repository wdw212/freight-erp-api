<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!app()->environment('testing')) {
            return;
        }

        $defaultConnection = (string)config('database.default');
        if ($defaultConnection === 'sqlite') {
            return;
        }

        $database = (string)config("database.connections.{$defaultConnection}.database");
        $host = (string)config("database.connections.{$defaultConnection}.host");

        throw new RuntimeException(sprintf(
            '测试环境禁止使用共享数据库。当前连接=%s host=%s database=%s。请先配置 .env.testing 或显式切到 sqlite/隔离测试库后再运行测试。',
            $defaultConnection,
            $host,
            $database
        ));
    }
}
