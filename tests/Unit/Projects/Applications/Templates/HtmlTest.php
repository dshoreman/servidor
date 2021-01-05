<?php

namespace Tests\Unit\Projects\Applications\Templates;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Projects\Application;
use Servidor\Projects\Project;
use Tests\TestCase;

class HtmlTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function document_root_defaults_to_system_dir(): void
    {
        $app = new Application([
            'template' => 'html',
            'source_repository' => 'test/foo',
        ]);
        $project = Project::create(['name' => 'htmlroot']);
        $project->applications()->save($app);

        $this->assertEquals('/var/www/htmlroot/foo', $app->document_root);
    }

    /** @test */
    public function getLogs_returns_empty_array(): void
    {
        $app = new Application(['template' => 'html']);

        $this->assertEmpty($app->template()->getLogs());
    }
}
