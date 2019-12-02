<?php

namespace Servidor\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidLinuxUser implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        return !str_contains($value, [':', ',', "\t", "\n", ' ']);
    }

    public function message(): string
    {
        return 'The :attribute contains invalid characters.';
    }
}
