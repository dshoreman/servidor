<?php

namespace Tests\Feature;

use Servidor\Site;
use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\RequiresAuth;

class SitesApiTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    /** @test */
    public function guest_cannot_list_sites()
    {
        $response = $this->getJson('/api/sites');

        $response->assertJsonCount(1);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    /** @test */
    public function authed_user_can_list_sites()
    {
        Site::create(['name' => 'Blog 1']);
        Site::create(['name' => 'Blog 2']);

        $response = $this->authed()->getJson('/api/sites');

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJson(Site::all()->toArray());
    }

    /** @test */
    public function guest_cannot_create_site()
    {
        $response = $this->postJson('/api/sites', [
            'name' => 'Test Site',
            'primary_domain' => 'example.com',
            'is_enabled' => true,
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(0, Site::count());
        $this->assertEquals(null, Site::first());
    }

    /** @test */
    public function authed_user_can_create_site()
    {
        $response = $this->authed()->postJson('/api/sites', [
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
        $response = $this->authed()->postJson('/api/sites', []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['name']);
        $this->assertNull(Site::first());
    }

    /** @test */
    public function cannot_create_site_with_invalid_data()
    {
        $response = $this->authed()->postJson('/api/sites', [
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
    public function guest_cannot_update_site()
    {
        $site = Site::create(['name' => 'My Blog']);

        $response = $this->putJson('/api/sites/'.$site->id, [
            'name' => 'My Updated Blog',
            'type' => 'basic',
            'source_repo' => 'https://github.com/user/blog.git',
            'document_root' => '/var/www/blog',
        ]);

        $updated = Site::find($site->id);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals('My Blog', $updated->name);
        $this->assertNull($updated->type);
        $this->assertNull($updated->source_repo);
        $this->assertNull($updated->document_root);
    }

    /** @test */
    public function authed_user_can_update_site()
    {
        $site = Site::create(['name' => 'My Other Blog']);

        $response = $this->authed()->putJson('/api/sites/'.$site->id, [
            'name' => 'My Updated Blog',
            'type' => 'basic',
            'source_repo' => 'https://github.com/user/blog.git',
            'document_root' => '/var/www/blog',
        ]);

        $updated = Site::find($site->id);

        $response->assertOk();
        $this->assertEquals('My Updated Blog', $updated->name);
        $this->assertEquals('basic', $updated->type);
        $this->assertEquals('https://github.com/user/blog.git', $updated->source_repo);
        $this->assertEquals('/var/www/blog', $updated->document_root);
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

    /** @test */
    public function guest_cannot_delete_site()
    {
        $site = Site::create(['name' => 'Primed for deletion']);

        $response = $this->deleteJson('/api/sites/'.$site->id);

        $response->assertJsonCount(1);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson(['message' => 'Unauthenticated.']);
        $this->assertArraySubset($site->toArray(), Site::first()->toArray());
    }

    /** @test */
    public function authed_user_can_delete_site()
    {
        $site = Site::create(['name' => 'Delete me!']);

        $response = $this->authed()->deleteJson('/api/sites/'.$site->id);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertNull(Site::find($site->id));
    }
}
