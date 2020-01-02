<?php

namespace Servidor\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

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
        unset($attribute);

        return !Str::contains($value, [':', ',', "\t", "\n", ' ']);
    }

    public function message(): string
    {
        return 'The :attribute contains invalid characters.';
    }
}
