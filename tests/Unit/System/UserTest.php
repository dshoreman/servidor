<?php

namespace Tests\Unit\System;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use PHPUnit\Framework\TestCase;
use Servidor\System\User as SystemUser;

class UserTest extends TestCase
{
    use ArraySubsetAsserts;

    /** @test */
    public function create_returns_a_valid_LinuxUser_array(): void
    {
        $user = SystemUser::create('jim4285');

        exec('sudo userdel jim4285');

        $this->assertIsArray($user);
        $this->assertArrayHasKey('uid', $user);
        $this->assertArrayHasKey('gid', $user);
        $this->assertArrayHasKey('groups', $user);
        $this->assertIsArray($user['groups']);
        $this->assertArraySubset([
            'name' => 'jim4285',
            'dir' => '/home/jim4285',
        ], $user);
    }

    /** @test */
    public function findByName_returns_instance_of_self(): void
    {
        $user = SystemUser::findByName('root');

        $this->assertInstanceOf(SystemUser::class, $user);
    }
}
