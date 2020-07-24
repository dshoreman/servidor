<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Site;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    public const PHP_LOG_PATH = '/var/log/php%d.%d-fpm.log';

    /** @test */
    public function logs_include_only_php_log_for_php_projects(): void
    {
        $site = Site::create(['name' => 'loggable', 'type' => 'php']);

        $logs = $site->logs;
        $logPath = ini_get('error_log')
            ?: sprintf(self::PHP_LOG_PATH, PHP_MAJOR_VERSION, PHP_MINOR_VERSION);

        $this->assertCount(1, $logs);
        $this->assertArrayHasKey('php', $logs);
        $this->assertEquals($logPath, $logs['php']['path']);
    }

    /** @test */
    public function logs_include_php_and_laravel_logs_for_laravel_projects(): void
    {
        $site = Site::create(['name' => 'loggable', 'type' => 'laravel']);

        $logs = $site->logs;
        $logPath = ini_get('error_log')
            ?: sprintf(self::PHP_LOG_PATH, PHP_MAJOR_VERSION, PHP_MINOR_VERSION);

        $this->assertCount(2, $logs);
        $this->assertArrayHasKey('php', $logs);
        $this->assertArrayHasKey('laravel', $logs);
        $this->assertEquals($logPath, $logs['php']['path']);
        $this->assertEquals('storage/logs/laravel.log', $logs['laravel']['path']);
    }

    /** @test */
    public function logs_is_empty_for_non_php_or_laravel_projects(): void
    {
        $site = Site::create(['name' => 'loggable', 'type' => 'basic']);

        $logs = $site->logs;
        $this->assertEmpty($logs);
    }

    /** @test */
    public function system_user_is_null_when_not_found(): void
    {
        $site = Site::create(['name' => 'ghosty']);
        $site->system_user = 2342;

        $this->assertEquals('ghosty', $site->name);
        $this->assertNull($site->systemUser);
    }
}
