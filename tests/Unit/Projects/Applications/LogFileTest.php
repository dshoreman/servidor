<?php

namespace Tests\Unit\Projects\Applications;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Projects\Application;
use Servidor\Projects\Applications\LogFile;
use Servidor\Projects\Project;
use Tests\TestCase;

class LogFileTest extends TestCase
{
    use RefreshDatabase;

    private $baseDir = '/home/logrel/laravel';

    private $laravelLog = '/storage/logs/laravel.log';

    /** @test */
    public function relative_paths_are_prefixed_with_docroot(): void
    {
        $project = Project::create(['name' => 'logrel']);
        $project->applications()->save($app = new Application([
            'template' => 'laravel',
            'source_provider' => 'github',
            'source_repository' => 'laravel/laravel',
            'source_branch' => 'master',
        ]));
        exec(sprintf(
            'sudo mkdir -p /home/logrel/laravel/storage/logs && sudo cp "%s" "%s"',
            resource_path('test-skel/logrel' . $this->laravelLog),
            $this->baseDir . $this->laravelLog,
        ));

        $log = new LogFile($app, 'My Test Log', ltrim($this->laravelLog, '/'));

        $this->assertEquals($this->baseDir . $this->laravelLog, $log->getPath());
        $this->assertEquals('It works!', (string) $log);
    }
}
