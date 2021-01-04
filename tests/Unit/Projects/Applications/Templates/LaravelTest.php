<?php

namespace Tests\Unit\Projects\Applications\Templates;

use Servidor\Projects\Application;
use Tests\TestCase;

class LaravelTest extends TestCase
{
    /** @test */
    public function getLogs_includes_php_and_laravel_logs(): void
    {
        $app = new Application(['template' => 'laravel']);
        $logs = $app->template()->getLogs();

        $this->assertCount(2, $logs);
        $this->assertArrayHasKey('php', $logs);
        $this->assertArrayHasKey('laravel', $logs);
        $this->assertEquals('PHP Error Log', $logs['php']->getTitle());
        $this->assertEquals('Laravel Log', $logs['laravel']->getTitle());
    }
}
