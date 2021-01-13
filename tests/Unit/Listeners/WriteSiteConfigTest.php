<?php

namespace Tests\Unit\Listeners;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Site;
use Tests\TestCase;

class WriteSiteConfigTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function enabling_site_creates_config_symlink(): void
    {
        $link = '/etc/nginx/sites-enabled/symlinkery.dev.conf';
        $target = '/etc/nginx/sites-available/symlinkery.dev.conf';

        $path = resource_path('test-skel/new-project');
        $this->assertFileNotExists($link);
        $site = Site::create([
            'name' => 'symlinkery',
        ]);
        $site->update([
            'project_root' => $path,
            'primary_domain' => 'symlinkery.dev',
            'source_repo' => 'https://github.com/dshoreman/servidor-test-site.git',
            'source_branch' => 'develop',
            'is_enabled' => true,
            'type' => 'basic',
        ]);

        $this->assertTrue(is_link($link));
        $this->assertFileExists($link);
        $this->assertEquals($target, readlink($link));

        exec("rm -rf \"{$site->project_root}\"; sudo rm \"{$link}\"");
    }

    /** @test */
    public function symlink_is_not_created_when_it_is_valid(): void
    {
        $link = '/etc/nginx/sites-enabled/symlinkeroo.dev.conf';
        $path = resource_path('test-skel/new-project');
        $site = Site::create(['name' => 'symlinkeroo']);

        $site->update([
            'project_root' => $path,
            'primary_domain' => 'symlinkeroo.dev',
            'source_repo' => 'https://github.com/dshoreman/servidor-test-site.git',
            'source_branch' => 'develop',
            'is_enabled' => true,
            'type' => 'basic',
        ]);

        $linkBefore = readlink($link);
        $site->update(['name' => 'symlinkeroo-updated']);

        $this->assertEquals('symlinkeroo-updated', $site->name);
        $this->assertSame($linkBefore, readlink($link));

        exec("rm -rf \"{$site->project_root}\"; sudo rm \"{$link}\"");
    }

    /** @test */
    public function outdated_symlinks_get_replaced(): void
    {
        $link = '/etc/nginx/sites-enabled/outdated.dev.conf';
        $vhost = '/etc/nginx/sites-available/outdated.dev.conf';
        $path = resource_path('test-skel/outdated-project');
        $site = Site::create(['name' => 'linkoutdated']);

        $site->project_root = $path;
        $site->update([
            'primary_domain' => 'outdated.dev',
            'source_repo' => 'https://github.com/dshoreman/servidor-test-site.git',
            'source_branch' => 'develop',
            'type' => 'basic',
        ]);

        exec('sudo ln -s /dev/null "' . $link . '"');
        $site->update([
            'is_enabled' => true,
        ]);

        $this->assertFileExists($link);
        $this->assertFileExists($vhost);
        $this->assertTrue(is_link($link));
        $this->assertEquals($vhost, readlink($link));

        exec("rm -rf \"{$site->project_root}\"; sudo rm \"{$link}\"");
    }

    /** @test */
    public function disabling_project_removes_nginx_symlink(): void
    {
        $site = Site::create(['name' => 'laratest']);
        $site->project_root = resource_path('test-skel/larafoo');
        $site->update([
            'source_repo' => 'https://github.com/dshoreman/servidor-test-site.git',
            'primary_domain' => 'laratest.dev',
            'source_branch' => 'master',
            'is_enabled' => true,
            'type' => 'laravel',
        ]);
        $this->assertFileExists('/etc/nginx/sites-enabled/laratest.dev.conf');

        $site->update(['is_enabled' => false]);

        $this->assertFileNotExists('/etc/nginx/sites-enabled/laratest.dev.conf');

        unlink(storage_path('app/vhosts/laratest.dev.conf'));
        exec('rm -rf "' . $site->project_root . '"');
    }
}
