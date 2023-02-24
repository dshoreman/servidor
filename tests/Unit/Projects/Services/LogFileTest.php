<?php

namespace Tests\Unit\Projects\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Projects\Project;
use Servidor\Projects\ProjectService;
use Servidor\Projects\Services\LogFile;
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
        $project->services()->save($service = new ProjectService([
            'template' => 'laravel',
            'config' => ['source' => [
                'provider' => 'github',
                'repository' => 'laravel/laravel',
                'branch' => 'master',
            ]],
        ]));
        exec(sprintf(
            'sudo mkdir -p /home/logrel/laravel/storage/logs && sudo cp "%s" "%s"',
            resource_path('test-skel/logrel' . $this->laravelLog),
            $this->baseDir . $this->laravelLog,
        ));

        $log = new LogFile($service, 'My Test Log', ltrim($this->laravelLog, '/'));

        $this->assertEquals($this->baseDir . $this->laravelLog, $log->getPath());
        $this->assertEquals('It works!', (string) $log);
    }
}
