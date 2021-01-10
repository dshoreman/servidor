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
}
