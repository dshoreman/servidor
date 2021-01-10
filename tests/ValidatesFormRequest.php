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

    private function validateFieldFails(string $field, $value): void
    {
        $this->assertFalse($this->validateField($field, $value));
    }

    private function validateFieldPasses(string $field, $value): void
    {
        $this->assertTrue($this->validateField($field, $value));
    }

    private function validateField(string $field, $value): bool
    {
        return $this->getValidator(
            [$field => $value],
            [$field => $this->rules[$field]],
        )->passes();
    }

    private function getValidator(array $data, array $rules = []): Validator
    {
        return $this->validator->make($data, $rules ?: $this->rules);
    }
}
