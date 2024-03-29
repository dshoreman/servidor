<?php

namespace Tests\Unit\System;

use Servidor\System\Group as SystemGroup;
use Servidor\System\Groups\GenericGroupSaveFailure;
use Tests\TestCase;

class GroupTest extends TestCase
{
    /** @test */
    public function calling_commitAdd_with_bad_args_should_throw_exception(): void
    {
        $this->expectException(GenericGroupSaveFailure::class);
        $this->expectExceptionMessage('Invalid argument to option');

        SystemGroup::create('foo" echo "sup');
    }

    /** @test */
    public function calling_commitAdd_with_bad_syntax_should_throw_exception(): void
    {
        $this->expectException(GenericGroupSaveFailure::class);
        $this->expectExceptionMessage('Invalid command syntax.');

        SystemGroup::create('foo"');
    }
}
