<?php

namespace Tests\Unit\Projects\Services\Templates;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Projects\Actions\SyncAppFiles;
use Servidor\Projects\Project;
use Servidor\Projects\ProjectService;
use Tests\TestCase;

class HtmlTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function document_root_defaults_to_system_dir(): void
    {
        $service = new ProjectService([
            'template' => 'html',
            'config' => ['source' => [
                'provider' => 'github',
                'repository' => 'dshoreman/servidor-test-site',
            ]],
        ]);
        $project = Project::create(['name' => 'htmlroot']);
        $project->services()->save($service);

        $this->assertEquals('/var/www/htmlroot/servidor-test-site', $service->document_root);
    }

    /** @test */
    public function getLogs_returns_empty_array(): void
    {
        $service = new ProjectService(['template' => 'html']);

        $this->assertEmpty($service->template()->getLogs());
    }

    /** @test */
    public function pull_creates_project_root_if_it_does_not_exist(): void
    {
        $path = '/home/pull-sans-root/servidor-test-site';
        $this->assertDirectoryDoesNotExist($path);

        $project = Project::create(['name' => 'pull sans root']);
        $project->services()->save($service = new ProjectService([
            'domain_name' => 'pullsansroot.com',
            'config' => ['source' => [
                'repository' => 'dshoreman/servidor-test-site',
                'provider' => 'github',
                'branch' => 'develop',
            ]],
            'template' => 'php',
        ]));
        (new SyncAppFiles($service))->execute();

        $this->assertDirectoryExists($path);
    }

    /** @test */
    public function pull_creates_root_with_correct_permissions(): void
    {
        $path = '/var/www/rootperms/servidor-test-site';
        $this->assertDirectoryDoesNotExist($path);

        $project = Project::create(['name' => 'rootperms']);
        $project->services()->save($service = new ProjectService([
            'domain_name' => 'rootperms.example',
            'config' => ['source' => [
                'repository' => 'dshoreman/servidor-test-site',
                'provider' => 'github',
                'branch' => 'develop',
            ]],
            'template' => 'html',
        ]));
        (new SyncAppFiles($service))->execute();

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
