<?php

namespace Servidor\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class NoWhitespace implements Rule
{
    /**
     * @param string $attribute @unused-param
     * @param mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        return !Str::contains((string) $value, ["\t", "\n", ' ']);
    }

    public function message(): string
    {
        return 'The :attribute cannot contain whitespace or newlines.';
    }
}
