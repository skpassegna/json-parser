<?php

declare(strict_types=1);

namespace Skpassegna\Json\Contracts;

interface PointerInterface
{
    /**
     * Get a value from a JSON document using a JSON Pointer
     *
     * @param string|array $document The JSON document
     * @param string $pointer The JSON Pointer string (e.g., "/foo/0/bar")
     * @return mixed The value at the pointer location
     * @throws \Skpassegna\Json\Exceptions\RuntimeException If pointer is invalid or not found
     */
    public function get(string|array $document, string $pointer): mixed;

    /**
     * Set a value in a JSON document using a JSON Pointer
     *
     * @param string|array $document The JSON document
     * @param string $pointer The JSON Pointer string
     * @param mixed $value The value to set
     * @param bool $mutate Whether to modify the original document
     * @return string|array The modified document
     * @throws \Skpassegna\Json\Exceptions\RuntimeException If pointer is invalid
     */
    public function set(string|array $document, string $pointer, mixed $value, bool $mutate = false): string|array;

    /**
     * Remove a value from a JSON document using a JSON Pointer
     *
     * @param string|array $document The JSON document
     * @param string $pointer The JSON Pointer string
     * @param bool $mutate Whether to modify the original document
     * @return string|array The modified document
     * @throws \Skpassegna\Json\Exceptions\RuntimeException If pointer is invalid or not found
     */
    public function remove(string|array $document, string $pointer, bool $mutate = false): string|array;

    /**
     * Check if a JSON Pointer exists in a document
     *
     * @param string|array $document The JSON document
     * @param string $pointer The JSON Pointer string
     * @return bool True if the pointer exists
     */
    public function has(string|array $document, string $pointer): bool;

    /**
     * Create a JSON Pointer string from path segments
     *
     * @param string[] $segments The path segments
     * @return string The JSON Pointer string
     */
    public function create(array $segments): string;
}
