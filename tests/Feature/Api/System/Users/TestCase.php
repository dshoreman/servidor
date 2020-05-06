<?php

namespace Tests\Feature\Api\System\Users;

use Tests\TestCase as BaseCase;

abstract class TestCase extends BaseCase
{
    protected string $endpoint = '/api/system/users';

    protected $expectedKeys = [
        'name',
        'dir',
        'groups',
        'shell',
        'gid',
        'uid',
    ];
}
