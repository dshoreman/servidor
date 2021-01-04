<?php

namespace Tests\Unit\Projects\Applications\Templates;

use Servidor\Projects\Application;
use Tests\TestCase;

class PhpTest extends TestCase
{
    /** @test */
    public function getLogs_includes_only_php_log(): void
    {
        $app = new Application(['template' => 'php']);
        $logs = $app->template()->getLogs();

        $this->assertCount(1, $logs);
        $this->assertArrayHasKey('php', $logs);
        $this->assertEquals('PHP Error Log', $logs['php']->getTitle());
    }
}
