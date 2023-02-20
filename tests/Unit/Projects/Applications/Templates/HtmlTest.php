<?php

namespace Tests\Unit\Projects\Applications\Templates;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Projects\Actions\SyncAppFiles;
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
            'config' => ['source' => [
                'provider' => 'github',
                'repository' => 'dshoreman/servidor-test-site',
            ]],
        ]);
        $project = Project::create(['name' => 'htmlroot']);
        $project->applications()->save($app);

        $this->assertEquals('/var/www/htmlroot/servidor-test-site', $app->document_root);
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
            'domain_name' => 'pullsansroot.com',
            'config' => ['source' => [
                'repository' => 'dshoreman/servidor-test-site',
                'provider' => 'github',
                'branch' => 'develop',
            ]],
            'template' => 'php',
        ]));
        (new SyncAppFiles($app))->execute();

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
            'config' => ['source' => [
                'repository' => 'dshoreman/servidor-test-site',
                'provider' => 'github',
                'branch' => 'develop',
            ]],
            'template' => 'html',
        ]));
        (new SyncAppFiles($app))->execute();

        $stat = system("stat -c '%a' \"{$path}\"");

        $this->assertDirectoryExists($path);
        $this->assertEquals(2755, $stat);
    }

    public static function tearDownAfterClass(): void
    {
        exec('grep ^pull-sans-root /etc/passwd && sudo userdel -r pull-sans-root 2>/dev/null');
        exec('sudo rm -rf /var/www/rootperms /var/www/symlinkery');
        exec('sudo rm -rf /etc/nginx/sites-enabled/symlinkery.dev.conf /etc/nginx/sites-available/symlinkery.dev.conf');
    }
}
