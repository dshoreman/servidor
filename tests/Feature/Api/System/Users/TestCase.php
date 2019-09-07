<?php

namespace Tests\Feature\Api\System\Users;

use Tests\TestCase as BaseCase;

abstract class TestCase extends BaseCase
{
    protected $endpoint = '/api/system/users';

    protected $expectedKeys = [
        'name',
        'passwd',
        'uid',
        'gid',
        'gecos',
        'dir',
        'shell',
    ];

    protected function endpoint(int $id = null): string
    {
        $endpoint = $this->endpoint;

        if ($id) {
            $endpoint .= '/'.$id;
        }

        return $endpoint;
    }
}
