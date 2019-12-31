<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Servidor\Site;
use Tests\RequiresAuth;
use Tests\TestCase;

class SitesApiTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    /** @test */
    public function guest_cannot_list_sites(): void
    {
        $response = $this->getJson('/api/sites');

        $response->assertJsonCount(1);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    /** @test */
    public function authed_user_can_list_sites(): void
    {
        Site::create(['name' => 'Blog 1']);
        Site::create(['name' => 'Blog 2']);

        $response = $this->authed()->getJson('/api/sites');

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJson(Site::all()->toArray());
    }

    /** @test */
    public function guest_cannot_create_site(): void
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
    public function authed_user_can_create_site(): void
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

        $site = Site::firstOrFail();
        $this->assertEquals('Test Site', $site->name);
        $this->assertEquals('example.com', $site->primary_domain);
        $this->assertTrue($site->is_enabled);
    }

    /** @test */
    public function cannot_create_site_without_required_fields(): void
    {
        $response = $this->authed()->postJson('/api/sites', []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['name']);
        $this->assertNull(Site::first());
    }

    /** @test */
    public function cannot_create_site_with_invalid_data(): void
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
    public function guest_cannot_update_site(): void
    {
        $site = Site::create(['name' => 'My Blog']);

        $response = $this->putJson('/api/sites/' . $site->id, [
            'name' => 'My Updated Blog',
            'type' => 'basic',
            'source_repo' => 'https://github.com/user/blog.git',
            'document_root' => '/var/www/blog',
        ]);

        $updated = Site::findOrFail($site->id);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals('My Blog', $updated->name);
        $this->assertNull($updated->type);
        $this->assertNull($updated->source_repo);
        $this->assertNull($updated->document_root);
    }

    /** @test */
    public function authed_user_can_update_site(): void
    {
        $site = Site::create(['name' => 'My Other Blog']);

        $response = $this->authed()->putJson('/api/sites/' . $site->id, [
            'name' => 'My Updated Blog',
            'type' => 'basic',
            'source_repo' => 'https://github.com/dshoreman/servidor-test-site.git',
            'document_root' => '/var/www/blog',
            'primary_domain' => 'test.com',
        ]);

        $updated = Site::findOrFail($site->id);

        $response->assertOk();
        $this->assertEquals('My Updated Blog', $updated->name);
        $this->assertEquals('basic', $updated->type);
        $this->assertEquals('https://github.com/dshoreman/servidor-test-site.git', $updated->source_repo);
        $this->assertEquals('/var/www/blog', $updated->document_root);
    }

    /** @test */
    public function can_update_site_while_retaining_the_same_name(): void
    {
        $site = Site::create(['name' => 'My New Blog']);

        $response = $this->putJson('/api/sites/' . $site->id, [
            'name' => 'My New Blog',
            'type' => 'redirect',
        ]);

        $response->assertJsonMissingValidationErrors(['name']);
    }

    /**
     * @test
     * @depends authed_user_can_list_sites
     */
    public function guest_cannot_pull_site_files(): void
    {
        $site = Site::create([
            'name' => 'Dummy Site',
            'type' => 'basic',
            'source_repo' => 'https://github.com/dshoreman/servidor-test-site.git',
        ]);

        $response = $this->postJson('/api/sites/' . $site->id . '/pull');

        $response->assertJsonCount(1);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson(['message' => 'Unauthenticated.']);
        $this->assertArraySubset($site->toArray(), Site::firstOrFail()->toArray());
    }

    /** @test */
    public function authed_user_can_pull_site_files(): void
    {
        // This would ideally be inside resources/test-skel somewhere, but
        // for some reason Travis has permission issues creating in there.
        $dir = storage_path('test-clone');

        $site = Site::create([
            'name' => 'Dummy Site',
            'type' => 'basic',
            'document_root' => $dir,
            'source_repo' => 'https://github.com/dshoreman/servidor-test-site.git',
        ]);

        $response = $this->authed()->postJson('/api/sites/' . $site->id . '/pull');

        $response->assertOk();
        $this->assertDirectoryExists($dir . '/.git');

        // If we try this in tearDown(), storage_path() complains that
        // path.storage class can't be found. No idea why, but it does.
        exec('rm -rf "' . $dir . '"');
    }

    /** @test */
    public function cannot_pull_site_when_type_is_redirect(): void
    {
        $site = Site::create([
            'name' => 'Primed for deletion',
            'type' => 'redirect',
        ]);

        $response = $this->authed()->postJson('/api/sites/' . $site->id . '/pull');

        $response->assertJsonCount(1);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson(['error' => 'Project type does not support pull.']);
    }

    /** @test */
    public function cannot_pull_site_when_missing_document_root(): void
    {
        $site = Site::create([
            'name' => 'Dummy Site',
            'type' => 'basic',
            'source_repo' => 'https://github.com/dshoreman/servidor-test-site.git',
        ]);

        $response = $this->authed()->postJson('/api/sites/' . $site->id . '/pull');

        $response->assertJsonCount(1);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson(['error' => 'Project is missing its document root!']);
    }

    /** @test */
    public function guest_cannot_delete_site(): void
    {
        $site = Site::create(['name' => 'Primed for deletion']);

        $response = $this->deleteJson('/api/sites/' . $site->id);

        $response->assertJsonCount(1);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson(['message' => 'Unauthenticated.']);
        $this->assertArraySubset($site->toArray(), Site::firstOrFail()->toArray());
    }

    /** @test */
    public function authed_user_can_delete_site(): void
    {
        $site = Site::create(['name' => 'Delete me!']);

        $response = $this->authed()->deleteJson('/api/sites/' . $site->id);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertNull(Site::find($site->id));
    }
}
