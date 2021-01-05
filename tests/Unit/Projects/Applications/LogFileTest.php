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

    private $laravelLog = 'storage/logs/laravel.log';

    /** @test */
    public function relative_paths_are_prefixed_with_docroot(): void
    {
        $project = Project::create(['name' => 'logrel']);
        $project->applications()->save($app = new Application(
            ['template' => 'laravel', 'source_repository' => 'some/lararepo'],
        ));
        exec(sprintf(
            'sudo mkdir -p /home/logrel/lararepo/storage/logs && sudo cp "%s" "%s"',
            resource_path('test-skel/logrel/laravel.log'),
            '/home/logrel/lararepo/' . $this->laravelLog,
        ));

        $log = new LogFile($app, 'My Test Log', $this->laravelLog);

        $this->assertEquals('/home/logrel/lararepo/' . $this->laravelLog, $log->getPath());
        $this->assertEquals('It works!', (string) $log);
    }
}
