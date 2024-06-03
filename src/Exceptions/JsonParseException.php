<?php

namespace Skpassegna\JsonParser\Exceptions;

class JsonParseException extends JsonException
{
    private $line;
    private $column;

    public function __construct(string $message, int $line, int $column, int $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->line = $line;
        $this->column = $column;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getColumn(): int
    {
        return $this->column;
    }
}