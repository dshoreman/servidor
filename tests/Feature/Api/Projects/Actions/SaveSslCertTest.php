<?php

namespace Tests\Feature\Api\Projects\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Projects\Project;
use Servidor\Projects\ProjectService;
use Tests\TestCase;

class SaveSslCertTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function certs_get_written_when_saving_project(): ProjectService
    {
        $cert = 'storage/certs/ssl-test/ssl.test.crt';
        $key = 'storage/certs/ssl-test/ssl.test.key';

        $this->assertFileDoesNotExist($cert);
        $this->assertFileDoesNotExist($key);

        $project = Project::create(['name' => 'SSL Test']);
        $project->services()->save($service = new ProjectService([
            'domain_name' => 'ssl.test',
            'config' => [
                'ssl' => true,
                'sslCertificate' => 'cert',
                'sslPrivateKey' => 'key',
            ],
        ]));

        $this->assertFileExists($cert);
        $this->assertFileExists($key);

        return $service;
    }

    /** @test */
    public function certs_also_work_for_redirects(): void
    {
        $cert = 'storage/certs/ssl-rtest/ssl.rtest.crt';
        $key = 'storage/certs/ssl-rtest/ssl.rtest.key';

        $this->assertFileDoesNotExist($cert);
        $this->assertFileDoesNotExist($key);

        $project = Project::create(['name' => 'SSL RTest']);
        $project->services()->save($service = new ProjectService([
            'domain_name' => 'ssl.rtest',
            'template' => 'redirect',
            'config' => [
                'redirect' => [
                    'target' => 'foo',
                    'type' => 301,
                ],
                'ssl' => true,
                'sslCertificate' => 'redirect cert',
                'sslPrivateKey' => 'redirect key',
            ],
        ]));

        $this->assertFileExists($cert);
        $this->assertFileExists($key);
    }

    /**
     * @test
     *
     * @depends certs_get_written_when_saving_project
     */
    public function certs_are_replaced_with_paths_in_the_database(ProjectService $service): void
    {
        $this->assertEquals(storage_path('certs/ssl-test/ssl.test.crt'), $service->config->get('sslCertificate'));
    }

    /** @test */
    public function certs_are_skipped_when_not_set(): void
    {
        $project = Project::create(['name' => 'NoSSL Test']);
        $project->services()->save($service = new ProjectService([
            'domain_name' => 'nossl.test',
            'config' => ['ssl' => true],
        ]));

        $this->assertDirectoryDoesNotExist('storage/certs/nossl-test');
    }

    public static function tearDownAfterClass(): void
    {
        if (is_dir('storage/certs/ssl-test')) {
            unlink('storage/certs/ssl-test/ssl.test.key');
            unlink('storage/certs/ssl-test/ssl.test.crt');
            rmdir('storage/certs/ssl-test');
        }

        if (is_dir('storage/certs/ssl-rtest')) {
            unlink('storage/certs/ssl-rtest/ssl.rtest.key');
            unlink('storage/certs/ssl-rtest/ssl.rtest.crt');
            rmdir('storage/certs/ssl-rtest');
        }
    }
}
