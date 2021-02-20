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
        $this->app['env'] = 'testing';

        $response = $this->get('/');

        $response->assertViewIs('servidor');
        $response->assertSee('router-view id="app"');
        $response->assertSee('/js/app.js');
    }

    /** @test */
    public function app_layout_uses_mix_in_local_environment(): void
    {
        $this->app['env'] = 'local';

        $response = $this->get('/');

        $response->assertDontSee('#message: "The Mix manifest does not exist.');
        $response->assertViewIs('servidor');
        $response->assertSee('router-view id="app"');
        $response->assertSee('/js/app.js');
    }

    /** @test */
    public function nonexistant_api_routes_respond_with_404(): void
    {
        $response = $this->authed()->getJson('/api/cornholio');

        $response->assertNotFound();
    }
}
