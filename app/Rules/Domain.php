<?php

namespace Servidor\Rules;

use Illuminate\Contracts\Validation\Rule;

class Domain implements Rule
{
    /**
     * @var string Regular expression to match against
     */
    private $match = '/^
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
     * Determine if the validation rule passes.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string $attribute
     * @param mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        return (bool) preg_match($this->match, $value);
    }

    public function message(): string
    {
        return 'The :attribute is not a valid FQDN.';
    }
}
