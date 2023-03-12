<?php

namespace Tests\Feature\Api\Projects\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Projects\Project;
use Tests\RequiresAuth;
use Tests\TestCase;

class NginxConfigGenerationTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    /**
     * @test
     *
     * @dataProvider configs
     */
    public function generates_valid_nginx_configs(string $tpl, bool $www, int $redirect, string $conf): void
    {
        $project = Project::create(['name' => 'Config Test']);
        $path = storage_path('app/vhosts/nginx.test.conf');

        $response = $this->authed()->postJson('/api/projects/' . $project->id . '/services', [
            'domain' => 'nginx.test',
            'includeWww' => $www,
            'template' => $tpl,
            'config' => [
                'redirectWww' => $redirect,
                'source' => [
                    'repository' => 'dshoreman/servidor-test-site',
                    'provider' => 'github',
                    'branch' => 'develop',
                ],
            ],
        ]);

        $response->assertStatus(201);
        $this->assertFileExists($path);
        $this->assertFileEquals('tests/Feature/Api/Projects/nginx-configs/' . $conf, $path);
    }

    public function configs(): array
    {
        return [
            ['html', false, 0, 'basic.nossl.no-www.conf'],
            ['html', true, 0, 'basic.nossl.any-www.conf'],
            ['php', false, 0, 'php.nossl.no-www.conf'],
            ['php', true, 0, 'php.nossl.any-www.conf'],
            ['php', true, 1, 'php.nossl.strip-www.conf'],
            ['php', true, -1, 'php.nossl.force-www.conf'],
        ];
    }

    public static function tearDownAfterClass(): void
    {
        exec('sudo userdel config-test; sudo rm -rf /home/config-test');
    }
}
