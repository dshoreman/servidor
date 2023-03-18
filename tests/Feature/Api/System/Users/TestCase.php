<?php

namespace Tests\Feature\Api\System\Users;

use Tests\TestCase as BaseCase;

abstract class TestCase extends BaseCase
{
    protected string $endpoint = '/api/system/users';

    /**
     * @var array<int, string>
     */
    protected array $expectedKeys = [
        'name',
        'dir',
        'groups',
        'shell',
        'gid',
        'uid',
    ];
}
