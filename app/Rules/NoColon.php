<?php

namespace Servidor\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class NoColon implements Rule
{
    /**
     * @param string $attribute @unused-param
     * @param mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        return !Str::contains((string) $value, ':');
    }

    public function message(): string
    {
        return 'The :attribute cannot contain a colon.';
    }
}
