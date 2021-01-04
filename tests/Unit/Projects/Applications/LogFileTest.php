<?php

namespace Tests\Unit\Projects\Applications;

use Servidor\Projects\Application;
use Servidor\Projects\Applications\LogFile;
use Tests\TestCase;

class LogFileTest extends TestCase
{
    /** @test */
    public function relative_paths_are_prefixed_with_docroot(): void
    {
        $path = 'test-skel/logrel/laravel.log';
        $app = new Application(['template' => 'laravel']);
        $log = new LogFile($app, 'My Test Log', $path);

        // TODO: The first path should be hard-coded when project root is done!
        $this->assertEquals($app->project_root . '/' . $path, $log->getPath());
    }
}
