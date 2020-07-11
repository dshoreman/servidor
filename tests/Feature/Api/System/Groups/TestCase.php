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
}
