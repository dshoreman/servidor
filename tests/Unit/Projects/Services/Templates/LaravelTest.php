<?php

namespace Tests\Unit\Projects\Services\Templates;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Projects\Project;
use Servidor\Projects\ProjectService;
use Tests\TestCase;

class LaravelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function document_root_includes_public_dir(): void
    {
        $service = new ProjectService([
            'template' => 'laravel',
            'config' => ['source' => [
                'provider' => 'github',
                'repository' => 'dshoreman/servidor-test-site',
            ]],
        ]);
        $project = Project::create(['name' => 'lararoot']);
        $project->services()->save($service);

        $this->assertEquals('/home/lararoot/servidor-test-site/public', $service->document_root);
    }

    /** @test */
    public function getLogs_includes_php_and_laravel_logs(): void
    {
        $service = new ProjectService([
            'template' => 'laravel',
            'config' => ['source' => [
                'provider' => 'github',
                'repository' => 'dshoreman/servidor-test-site',
            ]],
        ]);
        Project::create(['name' => 'laralog'])->services()->save($service);
        $logs = $service->template()->getLogs();

        $this->assertCount(2, $logs);
        $this->assertArrayHasKey('php', $logs);
        $this->assertArrayHasKey('laravel', $logs);
        $this->assertEquals('PHP Error Log', $logs['php']->getTitle());
        $this->assertEquals('Laravel Log', $logs['laravel']->getTitle());
    }
}
