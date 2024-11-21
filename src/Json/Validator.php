<?php

declare(strict_types=1);

namespace Skpassegna\Json\Json;

use Skpassegna\Json\Contracts\ValidatorInterface;
use Skpassegna\Json\Exceptions\InvalidArgumentException;
use Skpassegna\Json\Exceptions\RuntimeException;

class Validator implements ValidatorInterface
{
    private array $errors = [];

    /**
     * @inheritDoc
     */
    public function isValid(string $json): bool
    {
        try {
            json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            $this->errors = [];
            return true;
        } catch (\JsonException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function validateSchema(mixed $data, string|object $schema): bool
    {
        if (!class_exists('\JsonSchema\Validator')) {
            throw new RuntimeException('justinrainbow/json-schema package is required for schema validation');
        }

        try {
            $validator = new \JsonSchema\Validator();
            $schemaData = is_string($schema) ? json_decode($schema) : $schema;
            
            if ($schemaData === null) {
                throw new InvalidArgumentException('Invalid JSON schema provided');
            }

            $dataObject = is_string($data) ? json_decode($data) : (object)$data;
            $validator->validate($dataObject, $schemaData);

            if ($validator->isValid()) {
                $this->errors = [];
                return true;
            }

            $this->errors = array_map(
                fn(array $error): string => sprintf('[%s] %s', $error['property'], $error['message']),
                $validator->getErrors()
            );

            return false;
        } catch (\Exception $e) {
            throw new RuntimeException(
                sprintf('Schema validation error: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
