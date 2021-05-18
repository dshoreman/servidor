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
            'source_provider' => 'github',
            'source_repository' => 'dshoreman/servidor-test-site',
        ]);
        $project = Project::create(['name' => 'htmlroot']);
        $project->applications()->save($app);

        $this->assertEquals('/var/www/htmlroot/servidor-test-site', $app->document_root);
    }

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

        $app->template()->enable();
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

    /** @test */
    public function getLogs_returns_empty_array(): void
    {
        $app = new Application(['template' => 'html']);

        $this->assertEmpty($app->template()->getLogs());
    }

    /** @test */
    public function pull_creates_project_root_if_it_does_not_exist(): void
    {
        $path = '/home/pull-sans-root/servidor-test-site';
        $this->assertDirectoryDoesNotExist($path);

        $project = Project::create(['name' => 'pull sans root']);
        $project->applications()->save($app = new Application([
            'source_repository' => 'dshoreman/servidor-test-site',
            'source_provider' => 'github',
            'source_branch' => 'develop',
            'template' => 'php',
        ]));
        $app->template()->pullCode();

        $this->assertDirectoryExists($path);
    }

    /** @test */
    public function pull_creates_root_with_correct_permissions(): void
    {
        $path = '/var/www/rootperms/servidor-test-site';
        $this->assertDirectoryDoesNotExist($path);

        $project = Project::create(['name' => 'rootperms']);
        $project->applications()->save($app = new Application([
            'domain_name' => 'rootperms.example',
            'source_repository' => 'dshoreman/servidor-test-site',
            'source_provider' => 'github',
            'source_branch' => 'develop',
            'template' => 'html',
        ]));
        $app->template()->pullCode();

        $stat = system("stat -c '%a' \"{$path}\"");

        $this->assertDirectoryExists($path);
        $this->assertEquals(755, $stat);
    }

    public static function tearDownAfterClass(): void
    {
        exec('grep ^pull-sans-root /etc/passwd && sudo userdel -r pull-sans-root 2>/dev/null');
        exec('sudo rm -rf /var/www/rootperms /var/www/symlinkery');
        exec('sudo rm -rf /etc/nginx/sites-enabled/symlinkery.dev.conf /etc/nginx/sites-available/symlinkery.dev.conf');
    }
}
