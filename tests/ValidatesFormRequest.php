<?php

namespace Tests;

use Illuminate\Validation\Validator;

trait ValidatesFormRequest
{
    /**
     * @var array<int> mixed
     */
    private array $rules;

    /**
     * @var \Illuminate\Validation\Factory
     */
    private $validator;

    private function shouldValidate(string $formRequestClass): void
    {
        $this->rules = (new $formRequestClass())->rules();
        $this->validator = $this->app['validator'];
    }

    /**
     * @param array<string, mixed> $data
     */
    private function validateAll(array $data): bool
    {
        return $this->getValidator($data)->passes();
    }

    private function validateField(string $field, mixed $value): bool
    {
        return $this->getValidator(
            [$field => $value],
            [$field => $this->rules[$field]],
        )->passes();
    }

    private function validateFieldFails(string $field, mixed $value): void
    {
        $this->assertFalse(
            $this->validateField($field, $value),
            $this->validationMessage($field, $value, 'fail', 'passed'),
        );
    }

    private function validateFieldPasses(string $field, mixed $value): void
    {
        $this->assertTrue(
            $this->validateField($field, $value),
            $this->validationMessage($field, $value, 'pass', 'failed'),
        );
    }

    private function validateChildField(string $field, string $parent, mixed $value, bool $nested = true): bool
    {
        $glue = $nested ? '.*.' : '.';
        $rule = $parent . $glue . $field;
        $data = $nested ? [[$field => $value]] : [$field => $value];

        return $this->getValidator(
            [$parent => $data],
            [$rule => $this->rules[$rule]],
        )->passes();
    }

    private function validateChildFieldFails(string $field, string $parent, mixed $value, bool $nested = true): void
    {
        $glued = $nested ? "{$parent}.*.{$field}" : "{$parent}.{$field}";

        $this->assertFalse(
            $this->validateChildField($field, $parent, $value, $nested),
            $this->validationMessage($glued, $value, 'fail', 'passed'),
        );
    }

    private function validateChildFieldPasses(string $field, string $parent, mixed $value, bool $nested = true): void
    {
        $glued = $nested ? "{$parent}.*.{$field}" : "{$parent}.{$field}";

        $this->assertTrue(
            $this->validateChildField($field, $parent, $value, $nested),
            $this->validationMessage($glued, $value, 'pass', 'failed'),
        );
    }

    private function validateConfigField(string $property, mixed $value): bool
    {
        $data = [$property => $value];
        $rule = 'config.' . $property;

        if (str_contains($property, '.')) {
            [$key, $prop] = explode('.', $property);

            $data = [$key => [$prop => $value]];
        }

        return $this->getValidator(
            ['config' => $data],
            [$rule => $this->rules[$rule]],
        )->passes();
    }

    private function validateConfigFieldFails(string $field, mixed $value): void
    {
        $this->assertFalse(
            $this->validateConfigField($field, $value),
            $this->validationMessage('config.' . $field, $value, 'fail', 'passed'),
        );
    }

    private function validateConfigFieldPasses(string $field, mixed $value): void
    {
        $this->assertTrue(
            $this->validateConfigField($field, $value),
            $this->validationMessage('config.' . $field, $value, 'pass', 'failed'),
        );
    }

    private function validationMessage(string $field, mixed $value, string $expected, string $actual): string
    {
        return "Expected field '{$field}' to {$expected} validation with value '"
            . (\is_array($value) || \is_object($value)
            ? json_encode($value) : $value) . "', but it {$actual}.";
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $rules
     */
    private function getValidator(array $data, array $rules = []): Validator
    {
        return $this->validator->make($data, $rules ?: $this->rules);
    }
}
