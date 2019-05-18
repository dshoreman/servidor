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

    /** @test */
    public function cannot_create_site_without_required_fields()
    {
        $response = $this->postJson('/api/sites', []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['name']);
        $this->assertNull(Site::first());
    }

    /** @test */
    public function cannot_create_site_with_invalid_data()
    {
        $response = $this->postJson('/api/sites', [
            'name' => '',
            'primary_domain' => '',
            'is_enabled' => '',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors([
            'name',
            'primary_domain',
            'is_enabled',
        ]);
        $this->assertNull(Site::first());
    }

    /** @test */
    public function guest_can_update_site()
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

    /** @test */
    public function can_update_site_while_retaining_the_same_name()
    {
        $site = Site::create(['name' => 'My New Blog']);

        $response = $this->putJson('/api/sites/'.$site->id, [
            'name' => 'My New Blog',
            'type' => 'redirect',
        ]);

        $response->assertJsonMissingValidationErrors(['name']);
    }
}
