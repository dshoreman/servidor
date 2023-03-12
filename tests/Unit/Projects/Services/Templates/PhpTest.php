<?php

namespace Tests\Unit\Projects\Services\Templates;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Projects\Project;
use Servidor\Projects\ProjectService;
use Tests\TestCase;

class PhpTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function document_root_matches_project_root(): void
    {
        $service = new ProjectService([
            'template' => 'php',
            'config' => ['source' => [
                'provider' => 'github',
                'repository' => 'dshoreman/servidor-test-site',
            ]],
        ]);
        $project = Project::create(['name' => 'phproot']);
        $project->services()->save($service);

        $this->assertEquals('/home/phproot/servidor-test-site', $service->document_root);
    }

    /** @test */
    public function getLogs_includes_only_php_log(): void
    {
        $service = new ProjectService(['template' => 'php']);
        $logs = $service->template()->getLogs();

        $this->assertCount(1, $logs);
        $this->assertArrayHasKey('php', $logs);
        $this->assertEquals('PHP Error Log', $logs['php']->getTitle());
    }

    public static function tearDownAfterClass(): void
    {
        exec('sudo userdel phproot; sudo rm -rf /home/phproot');
    }
}
