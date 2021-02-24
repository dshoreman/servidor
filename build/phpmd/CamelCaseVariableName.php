<?php

namespace PHPMD\Rule\Servidor;

use PHPMD\Rule\Controversial\CamelCaseVariableName as BaseRule;

class CamelCaseVariableName extends BaseRule
{
    protected function isValid($variable): bool
    {
        if ('$_' === $variable->getImage()) {
            return true;
        }

        return parent::isValid($variable);
    }
}
