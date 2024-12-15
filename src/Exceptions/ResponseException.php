<?php

namespace SchulzeFelix\Sistrix\Exceptions;

use Exception;
use Throwable;

class ResponseException extends Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}