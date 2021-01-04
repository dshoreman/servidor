<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Site;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function document_root_matches_project_root_in_non_laravel_projects(): void
    {
        $site = Site::create(['name' => 'rootcheck', 'type' => 'basic']);
        $site->project_root = '/var/www/rootcheck';
        $site->public_dir = '/';

        $this->assertEquals('/var/www/rootcheck', $site->document_root);
    }

    /** @test */
    public function document_root_includes_public_dir_in_laravel_projects(): void
    {
        $site = Site::create(['name' => 'lararoot', 'type' => 'laravel']);

        $site->project_root = '/var/www/lararoot';
        $site->public_dir = '/public';

        $this->assertEquals('/var/www/lararoot/public', $site->document_root);
    }
}
