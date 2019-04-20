<?php

namespace Servidor\Rules;

use Illuminate\Contracts\Validation\Rule;

class Domain implements Rule
{
    /**
     * @var string Regular expression to match against
     */
    private $match = '/^
        (?=^.{1,253}$)
        ((
            (?!^.+:\/\/)
            ([\pL\pN\pS\pP]{1,63}\.)+
            (\.?([\pL\-]|xn\-\-[\pL\pN-]+)+\.?)
        )|(
            (?!^-)
            ([\pL\pN\-]{1,63}+\.?)
        ))$/ixu';

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
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
