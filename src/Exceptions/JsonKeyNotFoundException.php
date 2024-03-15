<?php

namespace Skpassegna\JsonParser\Exceptions;

use Exception;

class JsonKeyNotFoundException extends JsonException
{
    public function __construct(string $key, $code = 0, Exception $previous = null)
    {
        $message = "Key '$key' not found in JSON data.";
        parent::__construct($message, $code, $previous);
    }
}