<?php

namespace Tests\Feature\Api;

use Tests\RequiresAuth;
use Tests\TestCase;

class SystemInformationTest extends TestCase
{
    use RequiresAuth;

    /** @test */
    public function guest_cannot_retrieve_system_stats(): void
    {
        $response = $this->getJson('/api/system-info');

        $response->assertUnauthorized();
        $response->assertExactJson(['message' => 'Unauthenticated.']);
    }

    /** @test */
    public function authed_user_can_retrieve_system_stats(): void
    {
        $response = $this->authed()->getJson('/api/system-info');

        $response->assertOk();
        $response->assertJsonStructure([
            'cpu',
            'load_average' => ['1m', '5m', '15m'],
            'ram' => ['total', 'used', 'free'],
            'disk' => ['partition', 'total', 'used', 'used_pct', 'free'],
            'os' => ['name', 'distro', 'version'],
        ]);
    }
}
