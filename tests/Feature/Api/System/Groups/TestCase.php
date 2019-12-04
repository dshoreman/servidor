<?php

namespace Tests\Feature\Api\System\Groups;

use Tests\TestCase as BaseCase;

abstract class TestCase extends BaseCase
{
    protected $endpoint = '/api/system/groups';

    protected $expectedKeys = [
        'gid',
        'name',
        'users',
    ];

    protected function endpoint(?int $id = null): string
    {
        $endpoint = $this->endpoint;

        if ($id) {
            $endpoint .= '/' . $id;
        }

        return $endpoint;
    }
}
