<?php

declare(strict_types=1);

namespace Skpassegna\Json\Schema;

use Skpassegna\Json\Exceptions\ValidationException;

class Validator
{
    private array $errors = [];

    /**
     * Validate data against a JSON schema.
     *
     * @param mixed $data
     * @param array|object $schema
     * @return bool
     * @throws ValidationException
     */
    public function validate(mixed $data, array|object $schema): bool
    {
        $this->errors = [];
        $this->validateValue($data, $schema);
        return empty($this->errors);
    }

    /**
     * Get validation errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Validate a value against a schema.
     *
     * @param mixed $value
     * @param array|object $schema
     * @param string $path
     * @return void
     */
    private function validateValue(mixed $value, array|object $schema, string $path = ''): void
    {
        $schema = (array)$schema;

        if (isset($schema['type'])) {
            $this->validateType($value, $schema['type'], $path);
        }

        if (isset($schema['properties']) && is_array($value)) {
            $this->validateProperties($value, $schema['properties'], $path);
        }

        if (isset($schema['required']) && is_array($value)) {
            $this->validateRequired($value, $schema['required'], $path);
        }

        if (isset($schema['items']) && is_array($value)) {
            $this->validateItems($value, $schema['items'], $path);
        }

        if (isset($schema['minLength']) && is_string($value)) {
            $this->validateMinLength($value, $schema['minLength'], $path);
        }

        if (isset($schema['maxLength']) && is_string($value)) {
            $this->validateMaxLength($value, $schema['maxLength'], $path);
        }

        if (isset($schema['minimum']) && is_numeric($value)) {
            $this->validateMinimum($value, $schema['minimum'], $path);
        }

        if (isset($schema['maximum']) && is_numeric($value)) {
            $this->validateMaximum($value, $schema['maximum'], $path);
        }

        if (isset($schema['pattern']) && is_string($value)) {
            $this->validatePattern($value, $schema['pattern'], $path);
        }

        if (isset($schema['enum'])) {
            $this->validateEnum($value, $schema['enum'], $path);
        }
    }

    private function validateType(mixed $value, string $type, string $path): void
    {
        $valid = match ($type) {
            'string' => is_string($value),
            'number' => is_numeric($value),
            'integer' => is_int($value),
            'boolean' => is_bool($value),
            'array' => is_array($value),
            'object' => is_object($value) || (is_array($value) && array_keys($value) !== range(0, count($value) - 1)),
            'null' => is_null($value),
            default => false,
        };

        if (!$valid) {
            $this->addError($path, "Expected type '{$type}', got " . gettype($value));
        }
    }

    private function validateProperties(array $value, array $properties, string $path): void
    {
        foreach ($properties as $propertyName => $propertySchema) {
            if (array_key_exists($propertyName, $value)) {
                $this->validateValue(
                    $value[$propertyName],
                    $propertySchema,
                    $path ? "{$path}.{$propertyName}" : $propertyName
                );
            }
        }
    }

    private function validateRequired(array $value, array $required, string $path): void
    {
        foreach ($required as $requiredProperty) {
            if (!array_key_exists($requiredProperty, $value)) {
                $this->addError($path, "Missing required property: {$requiredProperty}");
            }
        }
    }

    private function validateItems(array $value, array|object $items, string $path): void
    {
        foreach ($value as $index => $item) {
            $this->validateValue($item, $items, $path ? "{$path}[{$index}]" : (string)$index);
        }
    }

    private function validateMinLength(string $value, int $minLength, string $path): void
    {
        if (mb_strlen($value) < $minLength) {
            $this->addError($path, "String length must be at least {$minLength}");
        }
    }

    private function validateMaxLength(string $value, int $maxLength, string $path): void
    {
        if (mb_strlen($value) > $maxLength) {
            $this->addError($path, "String length must be at most {$maxLength}");
        }
    }

    private function validateMinimum(int|float $value, int|float $minimum, string $path): void
    {
        if ($value < $minimum) {
            $this->addError($path, "Value must be greater than or equal to {$minimum}");
        }
    }

    private function validateMaximum(int|float $value, int|float $maximum, string $path): void
    {
        if ($value > $maximum) {
            $this->addError($path, "Value must be less than or equal to {$maximum}");
        }
    }

    private function validatePattern(string $value, string $pattern, string $path): void
    {
        if (!preg_match("/{$pattern}/", $value)) {
            $this->addError($path, "String does not match pattern: {$pattern}");
        }
    }

    private function validateEnum(mixed $value, array $enum, string $path): void
    {
        if (!in_array($value, $enum, true)) {
            $this->addError($path, "Value must be one of: " . implode(', ', array_map('strval', $enum)));
        }
    }

    private function addError(string $path, string $message): void
    {
        $this->errors[] = [
            'path' => $path,
            'message' => $message,
        ];
    }
}
