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
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match($this->match, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute is not a valid FQDN.';
    }
}
