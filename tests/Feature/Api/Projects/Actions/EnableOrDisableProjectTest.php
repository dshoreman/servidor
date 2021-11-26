<?php

namespace Tests\Feature\Api\Projects\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Projects\Actions\EnableOrDisableProject;
use Servidor\Projects\Application;
use Servidor\Projects\Project;
use Tests\TestCase;

class EnableOrDisableProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function enabling_project_creates_config_symlink(): Application
    {
        $this->assertFileDoesNotExist($link = '/etc/nginx/sites-enabled/symlinkery.dev.conf');

        $project = Project::create(['name' => 'symlinkery', 'is_enabled' => true]);
        $project->applications()->save($app = new Application([
            'domain_name' => 'symlinkery.dev',
            'source_repository' => 'dshoreman/servidor-test-site',
            'source_provider' => 'github',
            'source_branch' => 'develop',
        ]));

        $this->assertFileExists($link);
        $this->assertTrue(is_link($link));
        $this->assertEquals('/etc/nginx/sites-available/symlinkery.dev.conf', readlink($link));

        return $app;
    }

    /**
     * @test
     * @depends enabling_project_creates_config_symlink
     */
    public function enabling_project_does_not_create_symlink_when_already_valid(Application $app): void
    {
        $linkBefore = readlink($link = '/etc/nginx/sites-enabled/symlinkery.dev.conf');

        $app->source_branch = 'master';
        $app->save();

        $this->assertEquals('master', $app->source_branch);
        $this->assertSame($linkBefore, readlink($link));
    }

    /** @test */
    public function toggling_project_with_missing_domain_throws_exception(): void
    {
        $this->expectExceptionMessage('Project missing domain name');

        $project = Project::create(['name' => 'nodomain', 'is_enabled' => true]);
        $project->applications()->save($app = new Application([
            'source_repository' => 'dshoreman/servidor-test-site',
            'source_provider' => 'github',
            'source_branch' => 'develop',
        ]));

        new EnableOrDisableProject($app);
    }

    /**
     * @test
     * @depends enabling_project_creates_config_symlink
     */
    public function outdated_symlinks_get_replaced(Application $app): void
    {
        $link = '/etc/nginx/sites-enabled/symlinkery.dev.conf';
        exec("sudo rm {$link} && sudo ln -s /dev/null {$link}");
        $this->assertNotEquals($link, readlink($link));

        $app->source_branch = 'develop';
        $app->save();

        $this->assertFileExists($link);
        $this->assertFileExists($vhost = '/etc/nginx/sites-available/symlinkery.dev.conf');
        $this->assertTrue(is_link($link));
        $this->assertEquals($vhost, readlink($link));
    }

    /**
     * @test
     * @depends enabling_project_creates_config_symlink
     */
    public function disabling_project_removes_nginx_symlink(Application $app): void
    {
        $this->assertFileExists('/etc/nginx/sites-enabled/symlinkery.dev.conf');

        $app->project->is_enabled = false;
        $app->save();

        $this->assertFileDoesNotExist('/etc/nginx/sites-enabled/symlinkery.dev.conf');
    }
}
