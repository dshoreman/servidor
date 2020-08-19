<?php

namespace Tests\Unit\Http;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\RequiresAuth;
use Tests\TestCase;

class FallbackRouteTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    /** @test */
    public function frontend_routes_use_vue_layout(): void
    {
        $response = $this->get('/');

        $response->assertViewIs('servidor');
        $response->assertSee('router-view id="app"');
    }

    /** @test */
    public function nonexistant_api_routes_respond_with_404(): void
    {
        $response = $this->authed()->getJson('/api/cornholio');

        $response->assertNotFound();
    }
}
