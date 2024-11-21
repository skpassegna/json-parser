<?php

declare(strict_types=1);

namespace Skpassegna\Json\Json;

use ReflectionClass;
use ReflectionProperty;
use ReflectionType;
use ReflectionNamedType;
use ReflectionUnionType;
use Skpassegna\Json\Contracts\SchemaGeneratorInterface;
use Skpassegna\Json\Exceptions\RuntimeException;

/**
 * Generates JSON Schema from PHP classes using reflection
 */
class SchemaGenerator implements SchemaGeneratorInterface
{
    private Serializer $serializer;
    private array $processedClasses = [];

    public function __construct()
    {
        $this->serializer = new Serializer();
    }

    /**
     * Generate a JSON Schema from a PHP class
     *
     * @param string|object $class Class name or object instance
     * @param bool $draft2020 Use Draft 2020-12 instead of Draft-07
     * @return string Generated JSON Schema
     * @throws RuntimeException If schema generation fails
     */
    public function generate(string|object $class, bool $draft2020 = true): string
    {
        try {
            $this->processedClasses = [];
            $schema = $this->generateSchema($class, $draft2020);
            return $this->serializer->serialize($schema);
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to generate schema: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Generate schema for a class
     *
     * @param string|object $class Class name or object instance
     * @param bool $draft2020 Use Draft 2020-12
     * @return array<string,mixed> Generated schema
     * @throws \ReflectionException
     */
    private function generateSchema(string|object $class, bool $draft2020): array
    {
        $reflection = new ReflectionClass($class);
        $className = $reflection->getName();

        // Prevent infinite recursion
        if (isset($this->processedClasses[$className])) {
            return ['$ref' => "#/definitions/$className"];
        }
        $this->processedClasses[$className] = true;

        $schema = [
            '$schema' => $draft2020 
                ? 'https://json-schema.org/draft/2020-12/schema'
                : 'http://json-schema.org/draft-07/schema#',
            'type' => 'object',
            'title' => $reflection->getShortName(),
            'properties' => [],
            'required' => [],
        ];

        foreach ($reflection->getProperties() as $property) {
            $propertySchema = $this->generatePropertySchema($property, $draft2020);
            if ($propertySchema !== null) {
                $schema['properties'][$property->getName()] = $propertySchema;
                if (!$property->hasDefaultValue() && !$property->getType()?->allowsNull()) {
                    $schema['required'][] = $property->getName();
                }
            }
        }

        if (empty($schema['required'])) {
            unset($schema['required']);
        }

        return $schema;
    }

    /**
     * Generate schema for a property
     *
     * @param ReflectionProperty $property Property reflection
     * @param bool $draft2020 Use Draft 2020-12
     * @return array<string,mixed>|null Generated property schema
     * @throws \ReflectionException
     */
    private function generatePropertySchema(ReflectionProperty $property, bool $draft2020): ?array
    {
        $type = $property->getType();
        if (!$type) {
            return ['type' => 'mixed'];
        }

        if ($type instanceof ReflectionUnionType) {
            return $this->handleUnionType($type, $draft2020);
        }

        if ($type instanceof ReflectionNamedType) {
            return $this->handleNamedType($type, $draft2020);
        }

        return null;
    }

    /**
     * Handle union types (e.g., string|int)
     *
     * @param ReflectionUnionType $type Union type reflection
     * @param bool $draft2020 Use Draft 2020-12
     * @return array<string,mixed> Generated schema for union type
     * @throws \ReflectionException
     */
    private function handleUnionType(ReflectionUnionType $type, bool $draft2020): array
    {
        $types = [];
        foreach ($type->getTypes() as $unionType) {
            $typeSchema = $this->handleNamedType($unionType, $draft2020);
            if ($typeSchema !== null) {
                $types[] = $typeSchema;
            }
        }

        return ['anyOf' => $types];
    }

    /**
     * Handle named types (e.g., string, int, class names)
     *
     * @param ReflectionNamedType $type Named type reflection
     * @param bool $draft2020 Use Draft 2020-12
     * @return array<string,mixed>|null Generated schema for named type
     * @throws \ReflectionException
     */
    private function handleNamedType(ReflectionNamedType $type, bool $draft2020): ?array
    {
        $typeName = $type->getName();
        $nullable = $type->allowsNull();

        $schema = match($typeName) {
            'int', 'float' => ['type' => 'number'],
            'string' => ['type' => 'string'],
            'bool' => ['type' => 'boolean'],
            'array' => ['type' => 'array'],
            'object' => ['type' => 'object'],
            'mixed' => ['type' => ['string', 'number', 'boolean', 'object', 'array', 'null']],
            default => $this->handleCustomType($typeName, $draft2020)
        };

        if ($nullable && $schema !== null) {
            if (isset($schema['type']) && is_string($schema['type'])) {
                $schema['type'] = [$schema['type'], 'null'];
            }
        }

        return $schema;
    }

    /**
     * Handle custom class types
     *
     * @param string $className Class name
     * @param bool $draft2020 Use Draft 2020-12
     * @return array<string,mixed>|null Generated schema for custom type
     * @throws \ReflectionException
     */
    private function handleCustomType(string $className, bool $draft2020): ?array
    {
        if (!class_exists($className)) {
            return null;
        }

        return $this->generateSchema($className, $draft2020);
    }
}
