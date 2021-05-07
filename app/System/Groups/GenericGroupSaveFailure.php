<?php

namespace Servidor\System\Groups;

use Exception;
use Throwable;

class GenericGroupSaveFailure extends Exception
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        if (0 !== $code) {
            $message = sprintf('%s Exit code: %d.', $message, $code);
        }

        parent::__construct($message, $code, $previous);
    }
}
