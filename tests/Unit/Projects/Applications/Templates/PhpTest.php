<?php

namespace Tests\Unit\Projects\Applications\Templates;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Projects\Application;
use Servidor\Projects\Project;
use Tests\TestCase;

class PhpTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function document_root_matches_project_root(): void
    {
        $app = new Application([
            'template' => 'php',
            'source_provider' => 'github',
            'source_repository' => 'dshoreman/servidor-test-site',
        ]);
        $project = Project::create(['name' => 'phproot']);
        $project->applications()->save($app);

        $this->assertEquals('/home/phproot/servidor-test-site', $app->document_root);
    }

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
