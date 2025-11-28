<?php

declare(strict_types=1);

namespace Skpassegna\Json\Contracts;

/**
 * Reflection inspector interface for introspecting JSON documents.
 *
 * Provides comprehensive metadata about loaded JSON documents including
 * structure, schema inference, node information, and event listener details.
 */
interface ReflectionInspectorInterface
{
    /**
     * Get a description of the loaded document.
     *
     * @return array<string, mixed>
     */
    public function describeDocument(): array;

    /**
     * Get inferred schema from the loaded document.
     *
     * @return array<string, mixed>
     */
    public function inferSchema(): array;

    /**
     * Get metadata for a specific node by path.
     *
     * @param string $path JSON Pointer path
     * @return array<string, mixed>
     */
    public function getNodeMetadata(string $path): array;

    /**
     * Get information about all registered listeners.
     *
     * @return array<string, array<int, string>>
     */
    public function getListenerInfo(): array;

    /**
     * Get statistics about the document structure.
     *
     * @return array<string, mixed>
     */
    public function getStructureStats(): array;

    /**
     * Get a CLI-friendly dump of document information.
     *
     * @param bool $includeValues Whether to include actual values
     * @return string Formatted output
     */
    public function dump(bool $includeValues = false): string;

    /**
     * Get a JSON-serializable representation of all metadata.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
