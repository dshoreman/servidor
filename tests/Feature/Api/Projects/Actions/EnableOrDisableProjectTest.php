<?php

namespace Tests\Feature\Api\Projects\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Projects\Actions\EnableOrDisableProject;
use Servidor\Projects\Actions\MissingProjectData;
use Servidor\Projects\Project;
use Servidor\Projects\ProjectService;
use Tests\TestCase;

class EnableOrDisableProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function enabling_project_creates_config_symlink(): ProjectService
    {
        $this->assertFileDoesNotExist($link = '/etc/nginx/sites-enabled/symlinkery.dev.conf');

        $project = Project::create(['name' => 'symlinkery', 'is_enabled' => true]);
        $project->services()->save($service = new ProjectService([
            'domain_name' => 'symlinkery.dev',
            'config' => ['source' => [
                'repository' => 'dshoreman/servidor-test-site',
                'provider' => 'github',
                'branch' => 'develop',
            ]],
        ]));

        $this->assertFileExists($link);
        $this->assertTrue(is_link($link));
        $this->assertEquals('/etc/nginx/sites-available/symlinkery.dev.conf', readlink($link));

        return $service;
    }

    /**
     * @test
     *
     * @depends enabling_project_creates_config_symlink
     */
    public function enabling_project_does_not_create_symlink_when_already_valid(ProjectService $service): void
    {
        $linkBefore = readlink($link = '/etc/nginx/sites-enabled/symlinkery.dev.conf');

        $service->config = $service->config->replace(['source' => array_merge(
            $service->config->get('source'),
            ['branch' => 'master'],
        )]);
        $service->save();

        $this->assertEquals('master', $service->config->get('source')['branch']);
        $this->assertSame($linkBefore, readlink($link));
    }

    /** @test */
    public function toggling_project_with_missing_domain_throws_exception(): void
    {
        $this->expectExceptionObject(new MissingProjectData('domain name'));

        $project = Project::create(['name' => 'nodomain', 'is_enabled' => true]);
        $project->services()->save($service = new ProjectService([
            'config' => ['source' => [
                'repository' => 'dshoreman/servidor-test-site',
                'provider' => 'github',
                'branch' => 'develop',
            ]],
        ]));

        new EnableOrDisableProject($service);
    }

    /**
     * @test
     *
     * @depends enabling_project_creates_config_symlink
     */
    public function outdated_symlinks_get_replaced(ProjectService $service): void
    {
        $link = '/etc/nginx/sites-enabled/symlinkery.dev.conf';
        exec("sudo rm {$link} && sudo ln -s /dev/null {$link}");
        $this->assertNotEquals($link, readlink($link));

        $service->config = $service->config->replace(['source' => array_merge(
            $service->config->get('source'),
            ['branch' => 'develop'],
        )]);
        $service->save();

        $this->assertFileExists($link);
        $this->assertFileExists($vhost = '/etc/nginx/sites-available/symlinkery.dev.conf');
        $this->assertTrue(is_link($link));
        $this->assertEquals($vhost, readlink($link));
    }

    /**
     * @test
     *
     * @depends enabling_project_creates_config_symlink
     */
    public function disabling_project_removes_nginx_symlink(ProjectService $service): void
    {
        $this->assertFileExists('/etc/nginx/sites-enabled/symlinkery.dev.conf');

        $service->project->is_enabled = false;
        $service->save();

        $this->assertFileDoesNotExist('/etc/nginx/sites-enabled/symlinkery.dev.conf');
    }
}
