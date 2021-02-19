<?php

namespace Servidor\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class NoComma implements Rule
{
    public function passes($attribute, $value): bool
    {
        unset($attribute);

        return !Str::contains((string) $value, ',');
    }

    public function message(): string
    {
        return 'The :attribute cannot contain a comma.';
    }
}
