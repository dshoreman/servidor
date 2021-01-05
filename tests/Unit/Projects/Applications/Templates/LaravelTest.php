<?php

namespace Tests\Unit\Projects\Applications\Templates;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Projects\Application;
use Servidor\Projects\Project;
use Tests\TestCase;

class LaravelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function document_root_includes_public_dir(): void
    {
        $app = new Application([
            'template' => 'laravel',
            'source_repository' => 'test/foo',
        ]);
        $project = Project::create(['name' => 'lararoot']);
        $project->applications()->save($app);

        $this->assertEquals('/home/lararoot/foo/public', $app->document_root);
    }

    /** @test */
    public function getLogs_includes_php_and_laravel_logs(): void
    {
        $app = new Application(['template' => 'laravel', 'source_repository' => 'a/b']);
        Project::create(['name' => 'laralog'])->applications()->save($app);
        $logs = $app->template()->getLogs();

        $this->assertCount(2, $logs);
        $this->assertArrayHasKey('php', $logs);
        $this->assertArrayHasKey('laravel', $logs);
        $this->assertEquals('PHP Error Log', $logs['php']->getTitle());
        $this->assertEquals('Laravel Log', $logs['laravel']->getTitle());
    }
}
