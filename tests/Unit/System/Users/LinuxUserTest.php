<?php

namespace Tests\Unit\System\Users;

use PHPUnit\Framework\TestCase;
use Servidor\System\Users\LinuxUser;

class LinuxUserTest extends TestCase
{
    /** @test */
    public function disabling_toggle_arg_with_no_off_value_should_not_add_empty_element(): void
    {
        $user = new LinuxUser([
            'name' => 'foo',
        ]);

        $user->toggleArg(false, '--foo', '');

        $this->assertEquals([], $user->getArgs());
        $this->assertEquals('', $user->toArgs());
        $this->assertFalse($user->isDirty());
    }

    /** @test */
    public function multiple_toggles_of_same_arg_should_not_duplicate(): void
    {
        $user = new LinuxUser([
            'name' => 'foo',
        ]);

        $user->setUserGroup(true);
        $user->setUserGroup(false);
        $user->setUserGroup(true);

        $this->assertCount(1, $user->getArgs());
        $this->assertEquals(['-U'], array_values($user->getArgs()));
        $this->assertEquals('-U', $user->toArgs());
        $this->assertTrue($user->isDirty());
    }
}
