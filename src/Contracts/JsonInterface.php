<?php

declare(strict_types=1);

namespace Skpassegna\Json\Contracts;

interface JsonInterface
{
    /**
     * Parse JSON from a string, array, or object.
     *
     * @param string|array|object $input
     * @param array<string, mixed> $options
     * @return static
     */
    public static function parse(string|array|object $input, array $options = []): static;

    /**
     * Create a new empty JSON instance.
     *
     * @return static
     */
    public static function create(): static;

    /**
     * Get the underlying data.
     *
     * @return mixed
     */
    public function getData(): mixed;

    /**
     * Get a value by path.
     *
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    public function get(string $path, mixed $default = null): mixed;

    /**
     * Set a value at a path.
     *
     * @param string $path
     * @param mixed $value
     * @return static
     */
    public function set(string $path, mixed $value): static;

    /**
     * Remove a value at a path.
     *
     * @param string $path
     * @return static
     */
    public function remove(string $path): static;

    /**
     * Check if a path exists.
     *
     * @param string $path
     * @return bool
     */
    public function has(string $path): bool;

    /**
     * Validate against a JSON schema.
     *
     * @param array|object $schema
     * @return bool
     */
    public function validateSchema(array|object $schema): bool;

    /**
     * Convert to JSON string.
     *
     * @param int $options
     * @param int $depth
     * @return string
     */
    public function toString(int $options = 0, int $depth = 512): string;

    /**
     * Query using JSONPath.
     *
     * @param string $path
     * @return array
     */
    public function query(string $path): array;

    /**
     * Merge with another JSON.
     *
     * @param self|array|object $source
     * @param bool $recursive
     * @return static
     */
    public function merge(self|array|object $source, bool $recursive = true): static;
}
