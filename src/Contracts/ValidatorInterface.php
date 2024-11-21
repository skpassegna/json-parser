<?php

declare(strict_types=1);

namespace Skpassegna\Json\Contracts;

interface ValidatorInterface
{
    /**
     * Validate JSON string
     *
     * @param string $json The JSON string to validate
     * @return bool True if valid, false otherwise
     */
    public function isValid(string $json): bool;

    /**
     * Validate JSON against schema
     *
     * @param mixed $data The data to validate
     * @param string|object $schema The JSON schema
     * @return bool True if valid, false otherwise
     * @throws \Skpassegna\Json\Exceptions\InvalidArgumentException If schema is invalid
     */
    public function validateSchema(mixed $data, string|object $schema): bool;

    /**
     * Get validation errors
     *
     * @return array<string> List of validation errors
     */
    public function getErrors(): array;
}
