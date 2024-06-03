<?php

namespace Skpassegna\JsonParser\Exceptions;

class MalformedJsonException extends JsonException
{
    public function __construct(string $message, int $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}