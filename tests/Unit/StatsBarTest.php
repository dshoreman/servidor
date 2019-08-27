<?php

namespace Tests\Unit;

use Servidor\StatsBar;
use Tests\TestCase;

class StatsBarTest extends TestCase
{
    /** @test */
    public function stats_contains_all_expected_keys()
    {
        $data = StatsBar::stats();

        $this->assertIsArray($data);

        $this->assertArrayHasKey('cpu', $data);
        $this->assertIsFloat($data['cpu']);

        $this->assertArrayHasKey('ram', $data);
        $this->assertIsArray($data['ram']);
        $this->assertArrayHasKey('total', $data['ram']);
        $this->assertArrayHasKey('used', $data['ram']);
        $this->assertArrayHasKey('free', $data['ram']);

        $this->assertArrayHasKey('disk', $data);
        $this->assertIsArray($data['disk']);
        $this->assertArrayHasKey('total', $data['disk']);
        $this->assertArrayHasKey('used', $data['disk']);
        $this->assertArrayHasKey('free', $data['disk']);

        $this->assertArrayHasKey('hostname', $data);
        $this->assertIsString($data['hostname']);

        $this->assertArrayHasKey('os', $data);
        $this->assertIsArray($data['os']);
        $this->assertArrayHasKey('name', $data['os']);
        $this->assertArrayHasKey('distro', $data['os']);
        $this->assertArrayHasKey('version', $data['os']);
    }

    /** @test */
    public function hostname_matches_the_one_returned_by_php()
    {
        $data = StatsBar::stats();

        $this->assertSame(gethostname(), $data['hostname']);
    }

    /** @test */
    public function os_name_is_linux()
    {
        $data = StatsBar::stats();

        $this->assertSame('Linux', $data['os']['name']);
    }

    /** @test */
    public function free_memory_matches_total_minus_used()
    {
        $data = StatsBar::stats()['ram'];

        $this->assertEquals($data['total'] - $data['used'], $data['free']);
    }
}
