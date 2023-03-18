<?php

namespace Servidor\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Domain implements ValidationRule
{
    private string $match = '/^
        (?=^.{1,253}$)                          # Limit to 253 characters
        ((
            (?!^.+:\/\/)                        # Reject protocol prefixes
            ([\pL\pN\pS\pP]{1,63}\.)+           # Subdomains can contain symbols
            (\.?([\pL\-]|xn\-\-[\pL\pN-]+)+)    # But TLD cannot
        )|(
            (?!^-)                              # Hostnames cannot start with dash
            ([\pL\pN\-]{1,63}+)                 # but may contain dash and alphanums
        ))\.?$/ixu';

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string $attribute @unused-param
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match($this->match, (string) $value)) {
            $fail('The :attribute is not a valid FQDN.');
        }
    }
}
