<?php

declare(strict_types=1);

namespace Skpassegna\Json;

use JsonException;
use Skpassegna\Json\Contracts\JsonInterface;
use Skpassegna\Json\Exceptions\ParseException;
use Skpassegna\Json\Exceptions\ValidationException;
use Skpassegna\Json\Schema\Validator;
use Skpassegna\Json\Traits\DataAccessTrait;
use Skpassegna\Json\Traits\TransformationTrait;
use Skpassegna\Json\Utils\JsonPath;

class Json implements JsonInterface
{
    use DataAccessTrait;
    use TransformationTrait;

    private mixed $data;

    /**
     * Create a new JSON instance from a string, array, or object.
     *
     * @param string|array|object $input The input to parse
     * @param array<string, mixed> $options Parsing options
     * @throws ParseException If the input cannot be parsed
     */
    public static function parse(string|array|object $input, array $options = []): self
    {
        $instance = new self();

        if (is_string($input)) {
            try {
                $decoded = json_decode(
                    $input,
                    associative: true,
                    flags: JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING
                );
                $instance->data = $decoded;
            } catch (JsonException $e) {
                throw new ParseException("Failed to parse JSON: {$e->getMessage()}", previous: $e);
            }
        } else {
            $instance->data = $input;
        }

        return $instance;
    }

    /**
     * Create a new empty JSON instance.
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Get the underlying data.
     *
     * @return mixed The underlying data
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * Get a value by its path.
     *
     * @param string $path The path to the value (dot notation or JSONPath)
     * @param mixed $default The default value if the path doesn't exist
     * @return mixed The value at the path or the default value
     */
    public function get(string $path, mixed $default = null): mixed
    {
        if (str_starts_with($path, '$')) {
            return (new JsonPath($this->data))->query($path)[0] ?? $default;
        }

        $current = $this->data;
        $segments = explode('.', $path);

        foreach ($segments as $segment) {
            if (!is_array($current) || !array_key_exists($segment, $current)) {
                return $default;
            }
            $current = $current[$segment];
        }

        return $current;
    }

    /**
     * Set a value at a specific path.
     *
     * @param string $path The path where to set the value (dot notation)
     * @param mixed $value The value to set
     * @return self
     */
    public function set(string $path, mixed $value): self
    {
        $current = &$this->data;
        $segments = explode('.', $path);
        $last = array_pop($segments);

        foreach ($segments as $segment) {
            if (!isset($current[$segment]) || !is_array($current[$segment])) {
                $current[$segment] = [];
            }
            $current = &$current[$segment];
        }

        $current[$last] = $value;

        return $this;
    }

    /**
     * Remove a value at a specific path.
     *
     * @param string $path The path to remove (dot notation)
     * @return self
     */
    public function remove(string $path): self
    {
        $current = &$this->data;
        $segments = explode('.', $path);
        $last = array_pop($segments);

        foreach ($segments as $segment) {
            if (!isset($current[$segment]) || !is_array($current[$segment])) {
                return $this;
            }
            $current = &$current[$segment];
        }

        unset($current[$last]);

        return $this;
    }

    /**
     * Check if a path exists in the JSON.
     *
     * @param string $path The path to check (dot notation)
     * @return bool True if the path exists, false otherwise
     */
    public function has(string $path): bool
    {
        return $this->get($path, new \stdClass()) !== new \stdClass();
    }

    /**
     * Validate the JSON against a schema.
     *
     * @param array|object $schema The JSON schema to validate against
     * @return bool True if valid, false otherwise
     * @throws ValidationException If the schema is invalid
     */
    public function validateSchema(array|object $schema): bool
    {
        return (new Validator())->validate($this->data, $schema);
    }

    /**
     * Convert the JSON to a string.
     *
     * @param int $options JSON encoding options
     * @param int $depth Maximum depth
     * @return string The JSON string
     * @throws JsonException If encoding fails
     */
    public function toString(int $options = 0, int $depth = 512): string
    {
        return json_encode($this->data, JSON_THROW_ON_ERROR | $options, $depth);
    }

    /**
     * Query the JSON using JSONPath.
     *
     * @param string $path The JSONPath expression
     * @return array The matching values
     */
    public function query(string $path): array
    {
        return (new JsonPath($this->data))->query($path);
    }

    /**
     * Merge another JSON object into this one.
     *
     * @param Json|array|object $source The source to merge from
     * @param bool $recursive Whether to merge recursively
     * @return self
     */
    public function merge(Json|array|object $source, bool $recursive = true): self
    {
        $sourceData = $source instanceof Json ? $source->getData() : $source;
        
        if ($recursive) {
            $this->data = $this->mergeRecursive($this->data, $sourceData);
        } else {
            $this->data = array_merge((array)$this->data, (array)$sourceData);
        }

        return $this;
    }

    /**
     * Recursively merge arrays.
     *
     * @param array $target The target array
     * @param array $source The source array
     * @return array The merged array
     */
    private function mergeRecursive(array $target, array $source): array
    {
        foreach ($source as $key => $value) {
            if (is_array($value) && isset($target[$key]) && is_array($target[$key])) {
                $target[$key] = $this->mergeRecursive($target[$key], $value);
            } else {
                $target[$key] = $value;
            }
        }

        return $target;
    }
}
