<?php

namespace Tests\Unit\Http\Requests\System;

use Illuminate\Validation\Validator;
use Servidor\Http\Requests\System\CreateUser;
use Tests\TestCase;
use Tests\ValidatesFormRequest;

class CreateUserTest extends TestCase
{
    use ValidatesFormRequest;

    /**
     * @var array<string, array<int, string>|string>
     */
    public array $gidRule = ['gid' => 'required_unless:user_group,true'];

    public function setUp(): void
    {
        parent::setUp();

        $this->shouldValidate(CreateUser::class);
    }

    public function testGidIsRequiredWhenUserGroupIsMissing(): void
    {
        // fails when return trur
        $validator = $this->getValidator([], $this->gidRule);

        $this->shouldFail($validator, 'undefined');
    }

    public function testGidIsRequiredWhenUserGroupIsDisabled(): void
    {
        $validator = $this->getValidator(['user_group' => false], $this->gidRule);

        $this->shouldFail($validator, 'undefined');
    }

    public function testGidIsNotRequiredWhenUserGroupIsEnabled(): void
    {
        $validator = $this->getValidator(['user_group' => true], $this->gidRule);

        $this->shouldPass($validator, 'undefined');
    }

    public function testValidGidPassesWhenUserGroupIsMissing(): void
    {
        // fails when false
        $validator = $this->getValidator(['gid' => 1], $this->gidRule);

        $this->shouldPass($validator, 1);
    }

    public function testValidGidPassesWhenUserGroupIsDisabled(): void
    {
        $validator = $this->getValidator(['gid' => 1, 'user_group' => false], $this->gidRule);

        $this->shouldPass($validator, 1);
    }

    public function testValidGidPassesWhenUserGroupIsEnabled(): void
    {
        $validator = $this->getValidator(['gid' => 1, 'user_group' => true], $this->gidRule);

        $this->shouldPass($validator, 1);
    }

    private function shouldFail(Validator $validator, int|string $value): void
    {
        $message = $this->validationMessage('gid', $value, 'fail', 'passed');

        $this->assertFalse($validator->passes(), $message);
    }

    private function shouldPass(Validator $validator, int|string $value): void
    {
        $message = $this->validationMessage('gid', $value, 'pass', 'failed');

        $this->assertTrue($validator->passes(), $message);
    }
}
