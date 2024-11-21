<?php

declare(strict_types=1);

namespace Skpassegna\Json\Exceptions;

class ValidationException extends JsonException
{
    private array $errors;

    public function __construct(string $message, array $errors = [], int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getValidationErrors(): array
    {
        return $this->errors;
    }
}
