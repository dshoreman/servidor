<?php

namespace Servidor\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class NoColon implements ValidationRule
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string $attribute @unused-param
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (Str::contains((string) $value, ':')) {
            $fail('The :attribute cannot contain a colon.');
        }
    }
}
