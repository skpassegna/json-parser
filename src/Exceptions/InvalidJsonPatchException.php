<?php

namespace Skpassegna\JsonParser\Exceptions;

class InvalidJsonPatchException extends JsonException
{
    public function __construct(string $message, int $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}