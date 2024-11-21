<?php

declare(strict_types=1);

namespace Skpassegna\Json\Contracts;

interface PatchInterface
{
    /**
     * Apply JSON Patch operations to a JSON document
     *
     * @param string|array $document The JSON document to patch
     * @param string|array $patch The JSON Patch operations
     * @param bool $mutate Whether to modify the original document
     * @return string|array The patched document
     * @throws \Skpassegna\Json\Exceptions\RuntimeException If patch application fails
     */
    public function apply(string|array $document, string|array $patch, bool $mutate = false): string|array;

    /**
     * Generate a JSON Patch that transforms source into target
     *
     * @param string|array $source The source document
     * @param string|array $target The target document
     * @return string The JSON Patch operations
     * @throws \Skpassegna\Json\Exceptions\RuntimeException If diff generation fails
     */
    public function diff(string|array $source, string|array $target): string;

    /**
     * Test if a JSON Patch can be applied to a document
     *
     * @param string|array $document The JSON document to test
     * @param string|array $patch The JSON Patch operations to test
     * @return bool True if patch can be applied
     */
    public function test(string|array $document, string|array $patch): bool;
}
