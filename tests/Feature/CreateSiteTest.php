<?php

namespace Tests\Feature;

use App\Site;
use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateSiteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGuestCanCreateSite()
    {
        $response = $this->postJson('/api/sites', [
            'name' => 'Test Site',
            'primary_domain' => 'http://example.com',
            'is_enabled' => true,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment([
            'name' => 'Test Site',
            'primary_domain' => 'http://example.com',
            'is_enabled' => true,
        ]);

        $site = Site::first();
        $this->assertEquals('Test Site', $site->name);
        $this->assertEquals('http://example.com', $site->primary_domain);
        $this->assertTrue($site->is_enabled);
    }
}
