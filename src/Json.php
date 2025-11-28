<?php

declare(strict_types=1);

namespace Skpassegna\Json;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonException;
use Stringable;
use Traversable;
use Skpassegna\Json\Contracts\JsonInterface;
use Skpassegna\Json\Exceptions\ParseException;
use Skpassegna\Json\Exceptions\RuntimeException;
use Skpassegna\Json\Exceptions\ValidationException;
use Skpassegna\Json\Schema\Validator;
use Skpassegna\Json\Traits\DataAccessTrait;
use Skpassegna\Json\Traits\TransformationTrait;
use Skpassegna\Json\Utils\JsonPath;
use Skpassegna\Json\Utils\JsonPointer;
use Skpassegna\Json\Utils\JsonMerge;

class Json implements JsonInterface, ArrayAccess, IteratorAggregate, Countable, Stringable
{
    use DataAccessTrait;
    use TransformationTrait;

    /**
     * @var array|object
     */
    protected array|object $data = [];

    /**
     * @var JsonMutabilityMode
     */
    protected JsonMutabilityMode $mutabilityMode = JsonMutabilityMode::MUTABLE;

    /**
     * Constructor.
     *
     * @param array|object|mixed $data
     * @param JsonMutabilityMode $mutabilityMode
     */
    public function __construct(mixed $data = [], JsonMutabilityMode $mutabilityMode = JsonMutabilityMode::MUTABLE)
    {
        if (is_array($data) || is_object($data)) {
            $this->data = $data;
        } else {
            $this->data = [];
        }
        $this->mutabilityMode = $mutabilityMode;
    }

    /**
     * Create a new JSON instance from a string, array, or object.
     *
     * @param string|array|object $input The input to parse
     * @param array<string, mixed> $options Parsing options
     * @throws ParseException If the input cannot be parsed
     */
    public static function parse(string|array|object $input, array $options = []): static
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
    public static function create(): static
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
     * @return static
     */
    public function set(string $path, mixed $value): static
    {
        $this->guardMutable();
        
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
     * Remove a value at the specified path.
     *
     * @param string $path
     * @return $this
     */
    public function remove(string $path): static
    {
        $this->guardMutable();
        
        $segments = explode('.', $path);
        $current = &$this->data;
        $lastSegment = array_pop($segments);

        foreach ($segments as $segment) {
            if (!isset($current[$segment]) || (!is_array($current[$segment]) && !is_object($current[$segment]))) {
                return $this;
            }
            $current = &$current[$segment];
        }

        if (is_array($current)) {
            unset($current[$lastSegment]);
        } elseif (is_object($current)) {
            unset($current->$lastSegment);
        }

        return $this;
    }

    /**
     * Check if a path exists in the JSON data.
     *
     * @param string $path
     * @return bool
     */
    public function has(string $path): bool
    {
        $segments = explode('.', $path);
        $current = $this->data;

        foreach ($segments as $segment) {
            if (is_array($current)) {
                if (!array_key_exists($segment, $current)) {
                    return false;
                }
                $current = $current[$segment];
            } elseif (is_object($current)) {
                if (!property_exists($current, $segment)) {
                    return false;
                }
                $current = $current->$segment;
            } else {
                return false;
            }
        }

        return true;
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
     * @param JsonInterface|array $source The source to merge from
     * @param bool $recursive Whether to merge recursively
     * @return static
     */
    public function merge(JsonInterface|array $source, bool $recursive = true): static
    {
        $this->guardMutable();
        
        $sourceData = $source instanceof JsonInterface ? $source->getData() : $source;
        
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

    /**
     * Get a value using a JSON Pointer.
     *
     * @param string $pointer
     * @return mixed
     * @throws PathException
     */
    public function getPointer(string $pointer): mixed
    {
        return JsonPointer::get($this->data, $pointer);
    }

    /**
     * Set a value using a JSON Pointer.
     *
     * @param string $pointer
     * @param mixed $value
     * @return $this
     * @throws PathException
     */
    public function setPointer(string $pointer, mixed $value): static
    {
        $this->guardMutable();
        
        JsonPointer::set($this->data, $pointer, $value);
        return $this;
    }

    /**
     * Merge another JSON structure into this one.
     *
     * @param mixed $source
     * @param string $strategy
     * @return $this
     */
    public function mergeJson(mixed $source, string $strategy = JsonMerge::MERGE_RECURSIVE): static
    {
        $this->guardMutable();
        
        $this->data = JsonMerge::merge($this->data, $source, $strategy);
        return $this;
    }

    /**
     * Set the mutability mode.
     *
     * @param JsonMutabilityMode $mode
     * @return $this
     */
    public function setMutabilityMode(JsonMutabilityMode $mode): static
    {
        $this->mutabilityMode = $mode;
        return $this;
    }

    /**
     * Get the mutability mode.
     *
     * @return JsonMutabilityMode
     */
    public function getMutabilityMode(): JsonMutabilityMode
    {
        return $this->mutabilityMode;
    }

    /**
     * Check if this instance is mutable.
     *
     * @return bool
     */
    public function isMutable(): bool
    {
        return $this->mutabilityMode->isMutable();
    }

    /**
     * Check if this instance is immutable.
     *
     * @return bool
     */
    public function isImmutable(): bool
    {
        return $this->mutabilityMode->isImmutable();
    }

    // ============ ArrayAccess Implementation ============

    /**
     * Check whether an offset exists.
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        if (!is_array($this->data) && !is_object($this->data)) {
            return false;
        }
        return is_array($this->data) 
            ? array_key_exists($offset, $this->data)
            : property_exists($this->data, $offset);
    }

    /**
     * Get a value at an offset.
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        if (!$this->offsetExists($offset)) {
            return null;
        }
        return is_array($this->data)
            ? $this->data[$offset]
            : $this->data->$offset;
    }

    /**
     * Set a value at an offset.
     *
     * @param mixed $offset
     * @param mixed $value
     * @throws RuntimeException If the instance is immutable
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->guardMutable();
        
        if ($offset === null) {
            if (is_array($this->data)) {
                $this->data[] = $value;
            } else {
                throw new RuntimeException('Cannot append to non-array data using ArrayAccess');
            }
        } else {
            if (is_array($this->data)) {
                $this->data[$offset] = $value;
            } elseif (is_object($this->data)) {
                $this->data->$offset = $value;
            } else {
                throw new RuntimeException('Cannot set offset on non-array/non-object data');
            }
        }
    }

    /**
     * Unset a value at an offset.
     *
     * @param mixed $offset
     * @throws RuntimeException If the instance is immutable
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->guardMutable();
        
        if (is_array($this->data)) {
            unset($this->data[$offset]);
        } elseif (is_object($this->data)) {
            unset($this->data->$offset);
        }
    }

    // ============ IteratorAggregate Implementation ============

    /**
     * Get an iterator for the data.
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        if (is_array($this->data)) {
            return new ArrayIterator($this->data);
        }
        return new ArrayIterator((array)$this->data);
    }

    // ============ Countable Implementation ============

    /**
     * Count elements in the JSON data (inherited from DataAccessTrait, but made explicit here).
     *
     * @return int
     */
    public function count(): int
    {
        if (is_array($this->data)) {
            return count($this->data);
        }
        if (is_object($this->data)) {
            return count((array)$this->data);
        }
        return 0;
    }

    // ============ Stringable Implementation ============

    /**
     * Convert to string (magic method).
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    // ============ Magic Methods ============

    /**
     * Magic method to get a value by property name.
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return $this->get($name);
    }

    /**
     * Magic method to set a value by property name.
     *
     * @param string $name
     * @param mixed $value
     * @throws RuntimeException If the instance is immutable
     */
    public function __set(string $name, mixed $value): void
    {
        $this->set($name, $value);
    }

    /**
     * Magic method to check if a property exists.
     *
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return $this->has($name);
    }

    /**
     * Magic method to unset a property.
     *
     * @param string $name
     * @throws RuntimeException If the instance is immutable
     */
    public function __unset(string $name): void
    {
        $this->remove($name);
    }

    /**
     * Magic method to call instance as a function.
     *
     * @param string|null $path
     * @return mixed
     */
    public function __invoke(?string $path = null): mixed
    {
        if ($path === null) {
            return $this->data;
        }
        return $this->get($path);
    }

    /**
     * Magic method to call non-existent methods.
     *
     * @param string $method
     * @param array $arguments
     * @throws RuntimeException
     */
    public function __call(string $method, array $arguments): never
    {
        throw new RuntimeException("Call to undefined method " . static::class . "::{$method}()");
    }

    /**
     * Magic method to call non-existent static methods.
     *
     * @param string $method
     * @param array $arguments
     * @throws RuntimeException
     */
    public static function __callStatic(string $method, array $arguments): never
    {
        throw new RuntimeException("Call to undefined static method " . static::class . "::{$method}()");
    }

    /**
     * Magic method for debugging information.
     *
     * @return array
     */
    public function __debugInfo(): array
    {
        return [
            'data' => $this->data,
            'mutabilityMode' => $this->mutabilityMode->name,
        ];
    }

    /**
     * Magic method for cloning.
     *
     * @return void
     */
    public function __clone(): void
    {
        if (is_array($this->data)) {
            $this->data = array_map(static fn ($item) => is_object($item) ? clone $item : $item, $this->data);
        } elseif (is_object($this->data)) {
            $this->data = clone $this->data;
        }
        $this->mutabilityMode = JsonMutabilityMode::MUTABLE;
    }

    /**
     * Magic method for serialization.
     *
     * @return array
     */
    public function __serialize(): array
    {
        return [
            'data' => $this->data,
            'mutabilityMode' => $this->mutabilityMode,
        ];
    }

    /**
     * Magic method for unserialization.
     *
     * @param array $data
     * @return void
     */
    public function __unserialize(array $data): void
    {
        $this->data = $data['data'] ?? [];
        $this->mutabilityMode = $data['mutabilityMode'] ?? JsonMutabilityMode::MUTABLE;
    }
}
