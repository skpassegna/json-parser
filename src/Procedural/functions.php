<?php

declare(strict_types=1);

namespace Skpassegna\Json\Procedural;

use Skpassegna\Json\Json;
use Skpassegna\Json\Enums\DiffMergeStrategy;
use Skpassegna\Json\Exceptions\JsonException;

/**
 * Parse a JSON string into a Json instance.
 *
 * @param string $json The JSON string to parse
 * @param array<string, mixed> $options Parsing options (sanitize, max_depth, max_length)
 *
 * @return Json The parsed Json instance
 *
 * @throws \Skpassegna\Json\Exceptions\ParseException
 */
function json_parse(string $json, array $options = []): Json
{
    return Json::parse($json, $options);
}

/**
 * Create a new empty Json instance.
 *
 * @param mixed $data Optional initial data
 *
 * @return Json The new Json instance
 */
function json_create(mixed $data = []): Json
{
    return new Json($data);
}

/**
 * Get a value from JSON data by path.
 *
 * @param Json|array $data The data source
 * @param string $path The path to retrieve (dot notation or JSONPath)
 * @param mixed $default The default value if path not found
 *
 * @return mixed The value at the path, or default
 */
function json_get(Json|array $data, string $path, mixed $default = null): mixed
{
    if (!$data instanceof Json) {
        $data = new Json($data);
    }

    return $data->get($path, $default);
}

/**
 * Set a value in JSON data at the given path.
 *
 * @param Json $data The Json instance to modify
 * @param string $path The path where to set the value (dot notation)
 * @param mixed $value The value to set
 *
 * @return Json The modified Json instance for chaining
 */
function json_set(Json $data, string $path, mixed $value): Json
{
    return $data->set($path, $value);
}

/**
 * Remove a value from JSON data at the given path.
 *
 * @param Json $data The Json instance to modify
 * @param string $path The path to remove
 *
 * @return Json The modified Json instance for chaining
 */
function json_remove(Json $data, string $path): Json
{
    return $data->remove($path);
}

/**
 * Check if a path exists in JSON data.
 *
 * @param Json|array $data The data source
 * @param string $path The path to check
 *
 * @return bool True if the path exists, false otherwise
 */
function json_has(Json|array $data, string $path): bool
{
    if (!$data instanceof Json) {
        $data = new Json($data);
    }

    return $data->has($path);
}

/**
 * Query JSON data using JSONPath.
 *
 * @param Json|array $data The data source
 * @param string $path The JSONPath expression
 *
 * @return array The matching results
 */
function json_query(Json|array $data, string $path): array
{
    if (!$data instanceof Json) {
        $data = new Json($data);
    }

    return $data->query($path);
}

/**
 * Merge JSON data into a Json instance.
 *
 * @param Json $target The target Json instance
 * @param array|object|\Skpassegna\Json\Contracts\JsonInterface $source The source to merge
 * @param bool $recursive Whether to merge recursively (default: true)
 *
 * @return Json The merged Json instance for chaining
 */
function json_merge(Json $target, array|object $source, bool $recursive = true): Json
{
    return $target->merge($source, $recursive);
}

/**
 * Merge JSON data with a strategy.
 *
 * @param Json $target The target Json instance
 * @param array|object|\Skpassegna\Json\Contracts\JsonInterface $source The source to merge
 * @param DiffMergeStrategy|string $strategy The merge strategy to use
 *
 * @return Json The merged Json instance for chaining
 *
 * @throws JsonException If strategy execution fails
 */
function json_merge_with_strategy(Json $target, array|object $source, DiffMergeStrategy|string $strategy): Json
{
    if (is_string($strategy)) {
        $strategy = DiffMergeStrategy::from($strategy);
    }

    return $target->mergeWithStrategy($source, $strategy);
}

/**
 * Compute the difference between two data structures.
 *
 * @param Json|array $original The original data
 * @param Json|array $modified The modified data
 *
 * @return array The differences
 */
function json_diff(Json|array $original, Json|array $modified): array
{
    if (!$original instanceof Json) {
        $original = new Json($original);
    }

    if (!$modified instanceof Json) {
        $modified = new Json($modified);
    }

    return $original->diffWithStrategy($modified->getData(), DiffMergeStrategy::DIFF_STRUCTURAL);
}

/**
 * Compute the difference with a strategy.
 *
 * @param Json|array $original The original data
 * @param Json|array $modified The modified data
 * @param DiffMergeStrategy|string $strategy The diff strategy to use
 *
 * @return array The differences according to the strategy
 *
 * @throws JsonException If strategy execution fails
 */
function json_diff_with_strategy(Json|array $original, Json|array $modified, DiffMergeStrategy|string $strategy): array
{
    if (!$original instanceof Json) {
        $original = new Json($original);
    }

    if (!$modified instanceof Json) {
        $modified = new Json($modified);
    }

    if (is_string($strategy)) {
        $strategy = DiffMergeStrategy::from($strategy);
    }

    return $original->diffWithStrategy($modified->getData(), $strategy);
}

/**
 * Validate JSON data against a schema.
 *
 * @param Json|array $data The data to validate
 * @param array $schema The JSON schema
 *
 * @return bool True if valid, false otherwise
 *
 * @throws \Skpassegna\Json\Exceptions\ValidationException
 */
function json_validate(Json|array $data, array $schema): bool
{
    if (!$data instanceof Json) {
        $data = new Json($data);
    }

    return $data->validateSchema($schema);
}

/**
 * Convert JSON to a string.
 *
 * @param Json|array $data The data to convert
 * @param int $options JSON encoding options
 * @param int $depth Maximum depth
 *
 * @return string The JSON string
 *
 * @throws \JsonException If encoding fails
 */
function json_stringify(Json|array $data, int $options = 0, int $depth = 512): string
{
    if (!$data instanceof Json) {
        $data = new Json($data);
    }

    return $data->toString($options, $depth);
}

/**
 * Pretty print JSON to a string.
 *
 * @param Json|array $data The data to convert
 * @param int $depth Maximum depth
 *
 * @return string The pretty-printed JSON string
 *
 * @throws \JsonException If encoding fails
 */
function json_pretty(Json|array $data, int $depth = 512): string
{
    if (!$data instanceof Json) {
        $data = new Json($data);
    }

    return $data->toString(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES, $depth);
}

/**
 * Get the underlying data from a Json instance or return the data as-is.
 *
 * @param Json|array $data The data source
 *
 * @return array The underlying data
 */
function json_data(Json|array $data): array
{
    if ($data instanceof Json) {
        return $data->getData();
    }

    return $data;
}

/**
 * Count elements in JSON data.
 *
 * @param Json|array $data The data source
 *
 * @return int The number of elements
 */
function json_count(Json|array $data): int
{
    if (!$data instanceof Json) {
        $data = new Json($data);
    }

    return $data->count();
}

/**
 * Check if JSON data is empty.
 *
 * @param Json|array $data The data source
 *
 * @return bool True if empty, false otherwise
 */
function json_is_empty(Json|array $data): bool
{
    if (!$data instanceof Json) {
        $data = new Json($data);
    }

    return $data->isEmpty();
}

/**
 * Convert JSON to XML string.
 *
 * @param Json|array $data The data to convert
 *
 * @return string The XML string
 *
 * @throws \Skpassegna\Json\Exceptions\TransformException
 */
function json_to_xml(Json|array $data): string
{
    if (!$data instanceof Json) {
        $data = new Json($data);
    }

    return $data->toXml();
}

/**
 * Convert JSON to YAML string.
 *
 * @param Json|array $data The data to convert
 *
 * @return string The YAML string
 *
 * @throws \Skpassegna\Json\Exceptions\TransformException
 */
function json_to_yaml(Json|array $data): string
{
    if (!$data instanceof Json) {
        $data = new Json($data);
    }

    return $data->toYaml();
}

/**
 * Convert JSON to CSV string.
 *
 * @param Json|array $data The data to convert
 * @param array<string, mixed> $options CSV options
 *
 * @return string The CSV string
 *
 * @throws \Skpassegna\Json\Exceptions\TransformException
 */
function json_to_csv(Json|array $data, array $options = []): string
{
    if (!$data instanceof Json) {
        $data = new Json($data);
    }

    return $data->toCsv($options);
}

/**
 * Flatten JSON data into a single-dimensional array.
 *
 * @param Json|array $data The data to flatten
 * @param string $separator The separator for nested keys (default: '.')
 *
 * @return array The flattened array
 */
function json_flatten(Json|array $data, string $separator = '.'): array
{
    if (!$data instanceof Json) {
        $data = new Json($data);
    }

    $flattened = $data->flatten();
    $flatData = $flattened->getData();
    
    if (is_array($flatData)) {
        return $flatData;
    }
    
    return (array)$flatData;
}

/**
 * Unflatten a flat array back into nested structure.
 *
 * @param array $data The flat array to unflatten
 * @param string $separator The separator used in keys (default: '.')
 *
 * @return array The unflattened array
 */
function json_unflatten(array $data, string $separator = '.'): array
{
    $json = new Json($data);

    return $json->unflatten($separator)->getData();
}
