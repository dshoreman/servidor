<?php

namespace Tests\Feature;

use Servidor\Site;
use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SitesApiTest extends TestCase
{
    use RefreshDatabase;

    public function testGuestCanListSites()
    {
        Site::create(['name' => 'Blog 1']);
        Site::create(['name' => 'Blog 2']);

        $response = $this->getJson('/api/sites');
        $data = $response->getContent();

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJson(Site::all()->toArray());
    }

    public function testGuestCanCreateSite()
    {
        $response = $this->postJson('/api/sites', [
            'name' => 'Test Site',
            'primary_domain' => 'example.com',
            'is_enabled' => true,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment([
            'name' => 'Test Site',
            'primary_domain' => 'example.com',
            'is_enabled' => true,
        ]);

        $site = Site::first();
        $this->assertEquals('Test Site', $site->name);
        $this->assertEquals('example.com', $site->primary_domain);
        $this->assertTrue($site->is_enabled);
    }

    public function testGuestCanUpdateSite()
    {
        $site = Site::create(['name' => 'My Blog']);

        $response = $this->putJson('/api/sites/'.$site->id, [
            'name' => 'My Updated Blog',
            'type' => 'basic',
            'source_repo' => 'https://github.com/user/blog.git',
            'document_root' => '/var/www/blog',
        ]);

        $response->assertOk();
        $this->assertEquals('My Updated Blog', Site::find($site->id)->name);
    }

    public function testNameIsRequired()
    {
        $response = $this->postJson('/api/sites', ['name' => '']);

        $this->assertValidationErrors($response, 'name');

        $this->assertNull(Site::first());
    }

    public function testNameMustBeString()
    {
        $response = $this->postJson('/api/sites', ['name' => 42]);

        $this->assertValidationErrors($response, 'name');

        $this->assertNull(Site::first());
    }

    public function testNameMustBeUnique()
    {
        Site::create(['name' => 'Duplicate me!']);

        $response = $this->postJson('/api/sites', ['name' => 'Duplicate me!']);

        $this->assertValidationErrors($response, 'name');

        $this->assertEquals(1, Site::count());
    }

    public function testPrimaryDomainMustBeUrl()
    {
        $response = $this->postJson('/api/sites', [
            'name' => 'Good name',
            'primary_domain' => 'not a url',
        ]);

        $this->assertValidationErrors($response, 'primary_domain');

        $this->assertNull(Site::first());
    }

    public function testIsEnabledMustBeBoolean()
    {
        $response = $this->postJson('/api/sites', [
            'name' => 'Good name',
            'is_enabled' => 'true',
        ]);

        $this->assertValidationErrors($response, 'is_enabled');

        $this->assertNull(Site::first());
    }
}
