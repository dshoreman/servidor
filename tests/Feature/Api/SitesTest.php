<?php

namespace Tests\Feature\Api;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Servidor\Site;
use Tests\RequiresAuth;
use Tests\TestCase;

class SitesTest extends TestCase
{
    use ArraySubsetAsserts;
    use RefreshDatabase;
    use RequiresAuth;

    /** @test */
    public function guest_cannot_list_a_sites_branches(): void
    {
        $site = Site::create([
            'name' => 'Test Site',
            'type' => 'basic',
            'source_repo' => 'https://github.com/dshoreman/servidor-test-site.git',
        ]);

        $response = $this->getJson('/api/sites/' . $site->id . '/branches');

        $response->assertJsonCount(1);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    /** @test */
    public function authed_user_can_list_a_sites_branches(): void
    {
        $site = Site::create([
            'name' => 'Test Site',
            'type' => 'basic',
            'source_repo' => 'https://github.com/dshoreman/servidor-test-site.git',
        ]);

        $response = $this->authed()->getJson('/api/sites/' . $site->id . '/branches');

        $response->assertOk();
        $response->assertJson(['develop', 'master']);
    }

    /** @test */
    public function listing_branches_requires_site_to_have_a_repo(): void
    {
        $site = Site::create(['name' => 'Repoless', 'type' => 'basic']);

        $response = $this->authed()->getJson('/api/sites/' . $site->id . '/branches');

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['repo' => 'Missing repo']);
    }

    /** @test */
    public function guest_cannot_update_site(): void
    {
        $site = Site::create(['name' => 'My Blog']);

        $response = $this->putJson('/api/sites/' . $site->id, [
            'name' => 'My Updated Blog',
            'type' => 'basic',
            'source_repo' => 'https://github.com/user/blog.git',
            'project_root' => '/var/www/blog',
        ]);

        $updated = Site::findOrFail($site->id);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals('My Blog', $updated->name);
        $this->assertNull($updated->type);
        $this->assertNull($updated->source_repo);
        $this->assertNull($updated->project_root);
    }

    /** @test */
    public function authed_user_can_update_site(): void
    {
        $site = Site::create(['name' => 'My Other Blog']);

        $response = $this->authed()->putJson('/api/sites/' . $site->id, [
            'name' => 'My Updated Blog',
            'type' => 'basic',
            'source_repo' => 'https://github.com/dshoreman/servidor-test-site.git',
            'project_root' => '/var/www/blog',
            'primary_domain' => 'test.com',
        ]);

        $updated = Site::findOrFail($site->id);

        $response->assertOk();
        $this->assertEquals('My Updated Blog', $updated->name);
        $this->assertEquals('basic', $updated->type);
        $this->assertEquals('https://github.com/dshoreman/servidor-test-site.git', $updated->source_repo);
        $this->assertEquals('/var/www/blog', $updated->project_root);
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
            'project_root' => $dir,
            'source_repo' => 'https://github.com/dshoreman/servidor-test-site.git',
        ]);

        $response = $this->authed()->postJson('/api/sites/' . $site->id . '/pull');

        $response->assertOk();
        $this->assertDirectoryExists($dir . '/.git');

        exec('rm -rf "' . $site->project_root . '"');
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
    public function cannot_pull_site_when_missing_project_root(): void
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
    public function can_checkout_after_initial_pull(): void
    {
        $dir = resource_path('test-skel/checkedout');
        $site = Site::create([
            'name' => 'Site for checkout',
            'type' => 'basic',
            'project_root' => $dir,
            'source_repo' => 'https://github.com/dshoreman/servidor-test-site.git',
        ]);
        $site->update(['is_enabled' => true]);

        $response = $this->authed()->postJson('/api/sites/' . $site->id . '/pull');

        $response->assertOk();
        $response->assertJsonFragment([
            'name' => 'Site for checkout',
            'project_root' => $dir,
            'is_enabled' => true,
        ]);

        exec('rm -rf "' . $dir . '"');
    }

    /** @test */
    public function pull_creates_docroot_if_it_doesnt_exist(): void
    {
        $dir = resource_path('test-skel/makeme');
        $site = Site::create([
            'type' => 'basic',
            'name' => 'Creating Docroot',
            'project_root' => $dir,
            'source_repo' => 'https://github.com/dshoreman/servidor-test-site.git',
        ]);

        $this->assertDirectoryNotExists($dir);
        $response = $this->authed()->postJson('/api/sites/' . $site->id . '/pull');

        $response->assertOk();
        $response->assertJsonFragment([
            'name' => 'Creating Docroot',
            'project_root' => $dir,
            'type' => 'basic',
        ]);
        $this->assertDirectoryExists($dir);

        exec('rm -rf "' . $dir . '"');
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
