<?php

namespace Tests;

use Illuminate\Validation\Validator;

trait ValidatesFormRequest
{
    /**
     * @var array
     */
    private $rules;

    /**
     * @var \Illuminate\Validation\Factory
     */
    private $validator;

    private function shouldValidate(string $formRequestClass): void
    {
        $this->rules = (new $formRequestClass())->rules();
        $this->validator = $this->app['validator'];
    }

    private function validateAll(array $data): bool
    {
        return $this->getValidator($data)->passes();
    }

    private function validateField(string $field, $value): bool
    {
        return $this->getValidator(
            [$field => $value],
            [$field => $this->rules[$field]],
        )->passes();
    }

    private function validateFieldFails(string $field, $value): void
    {
        $this->assertFalse(
            $this->validateField($field, $value),
            $this->validationMessage($field, $value, 'fail', 'passed'),
        );
    }

    private function validateFieldPasses(string $field, $value): void
    {
        $this->assertTrue(
            $this->validateField($field, $value),
            $this->validationMessage($field, $value, 'pass', 'failed'),
        );
    }

    private function validateChildField(string $field, string $parent, $value): bool
    {
        $rule = "{$parent}.*.{$field}";

        return $this->getValidator(
            [$parent => [[$field => $value]]],
            [$rule => $this->rules[$rule]],
        )->passes();
    }

    private function validateChildFieldFails(string $field, string $parent, $value): void
    {
        $this->assertFalse(
            $this->validateChildField($field, $parent, $value),
            $this->validationMessage("{$parent}.*.{$field}", $value, 'fail', 'passed'),
        );
    }

    private function validateChildFieldPasses(string $field, string $parent, $value): void
    {
        $this->assertTrue(
            $this->validateChildField($field, $parent, $value),
            $this->validationMessage("{$parent}.*.{$field}", $value, 'pass', 'failed'),
        );
    }

    private function validationMessage(string $field, $value, string $expected, string $actual): string
    {
        return "Expected field '{$field}' to {$expected} validation with value '"
            . (is_array($value) || is_object($value)
            ? json_encode($value) : $value) . "', but it {$actual}.";
    }

    private function getValidator(array $data, array $rules = []): Validator
    {
        return $this->validator->make($data, $rules ?: $this->rules);
    }
}
