<?php

declare(strict_types=1);

namespace Skpassegna\Json\Traits;

use Skpassegna\Json\Exceptions\RuntimeException;
use Skpassegna\Json\JsonMutabilityMode;
use Skpassegna\Json\Utils\ArrayHelpers;

trait DataAccessTrait
{
    /**
     * Guard against mutations when in immutable mode.
     *
     * @throws RuntimeException If the instance is immutable
     */
    protected function guardMutable(): void
    {
        if (property_exists($this, 'mutabilityMode') && $this->mutabilityMode->isImmutable()) {
            throw new RuntimeException('Cannot mutate an immutable JSON instance. Use clone() or set mutable mode to modify data.');
        }
    }

    /**
     * Assert that a key exists in the data.
     *
     * @throws RuntimeException If the key does not exist
     */
    protected function assertKeyExists(string|int $key): void
    {
        if (is_array($this->data)) {
            if (!array_key_exists($key, $this->data)) {
                throw new RuntimeException("Key '{$key}' does not exist in JSON data.");
            }
        } elseif (is_object($this->data)) {
            if (!property_exists($this->data, $key)) {
                throw new RuntimeException("Property '{$key}' does not exist in JSON object.");
            }
        } else {
            throw new RuntimeException('Cannot access keys on non-array/non-object data.');
        }
    }

    /**
     * Get all keys at the current level.
     *
     * @return array<string|int>
     */
    public function keys(): array
    {
        return is_array($this->data) ? array_keys($this->data) : [];
    }

    /**
     * Get all values at the current level.
     *
     * @return array
     */
    public function values(): array
    {
        return is_array($this->data) ? array_values($this->data) : [];
    }

    /**
     * Count the number of elements.
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

    /**
     * Check if the JSON is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        if (is_array($this->data)) {
            return empty($this->data);
        }
        return empty($this->data);
    }

    /**
     * Get the JSON data as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return (array)$this->data;
    }

    /**
     * Get the JSON data as an object.
     *
     * @return object
     */
    public function toObject(): object
    {
        return (object)$this->data;
    }

    /**
     * Filter the JSON data using a callback.
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): static
    {
        if (!is_array($this->data)) {
            return $this;
        }

        $filtered = array_filter($this->data, $callback, ARRAY_FILTER_USE_BOTH);
        $new = clone $this;
        $new->data = $filtered;
        if (property_exists($new, 'mutabilityMode')) {
            $new->mutabilityMode = JsonMutabilityMode::MUTABLE;
        }

        return $new;
    }

    /**
     * Map the JSON data using a callback.
     *
     * @param callable $callback
     * @return static
     */
    public function map(callable $callback): static
    {
        if (!is_array($this->data)) {
            return $this;
        }

        $mapped = array_map($callback, $this->data);
        $new = clone $this;
        $new->data = $mapped;
        if (property_exists($new, 'mutabilityMode')) {
            $new->mutabilityMode = JsonMutabilityMode::MUTABLE;
        }

        return $new;
    }

    /**
     * Reduce the JSON data using a callback.
     *
     * @param callable $callback
     * @param mixed $initial
     * @return mixed
     */
    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        if (!is_array($this->data)) {
            return $initial;
        }

        return array_reduce($this->data, $callback, $initial);
    }

    /**
     * Sort the JSON data.
     *
     * @param callable|null $callback
     * @return static
     */
    public function sort(?callable $callback = null): static
    {
        if (!is_array($this->data)) {
            return $this;
        }

        $sorted = $this->data;
        if ($callback) {
            uasort($sorted, $callback);
        } else {
            asort($sorted);
        }

        $new = clone $this;
        $new->data = $sorted;
        if (property_exists($new, 'mutabilityMode')) {
            $new->mutabilityMode = JsonMutabilityMode::MUTABLE;
        }

        return $new;
    }

    /**
     * Get a slice of the JSON data.
     *
     * @param int $offset
     * @param int|null $length
     * @return static
     */
    public function slice(int $offset, ?int $length = null): static
    {
        if (!is_array($this->data)) {
            return $this;
        }

        $sliced = array_slice($this->data, $offset, $length);
        $new = clone $this;
        $new->data = $sliced;
        if (property_exists($new, 'mutabilityMode')) {
            $new->mutabilityMode = JsonMutabilityMode::MUTABLE;
        }

        return $new;
    }

    /**
     * Find elements in the JSON data that match a condition.
     *
     * @param callable $callback
     * @return array
     */
    public function find(callable $callback): array
    {
        if (!is_array($this->data)) {
            return [];
        }

        $results = [];
        array_walk_recursive($this->data, function ($value, $key) use ($callback, &$results) {
            if ($callback($value, $key)) {
                $results[] = $value;
            }
        });

        return $results;
    }

    /**
     * Get the first element that matches a condition.
     *
     * @param callable|null $callback
     * @return mixed
     */
    public function first(?callable $callback = null): mixed
    {
        if (!is_array($this->data)) {
            return null;
        }

        if ($callback === null) {
            return reset($this->data);
        }

        foreach ($this->data as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Get the last element that matches a condition.
     *
     * @param callable|null $callback
     * @return mixed
     */
    public function last(?callable $callback = null): mixed
    {
        if (!is_array($this->data)) {
            return null;
        }

        if ($callback === null) {
            return end($this->data);
        }

        $result = null;
        foreach ($this->data as $key => $value) {
            if ($callback($value, $key)) {
                $result = $value;
            }
        }

        return $result;
    }

    /**
     * Get a value at the specified path.
     *
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    public function get(string $path, mixed $default = null): mixed
    {
        if (empty($path)) {
            return $this->data;
        }

        // Ensure data is an array for traversal
        $current = (array)$this->data;

        $segments = explode('.', $path);

        foreach ($segments as $segment) {
            if (is_array($current) && array_key_exists($segment, $current)) {
                $current = $current[$segment];
            } else {
                return $default; // Return default if path does not exist
            }
        }

        return $current;
    }

    /**
     * Set a value at the specified path.
      *
      * @param string $path
      * @param mixed $value
      * @return $this
      */
     public function set(string $path, mixed $value): static
     {
        $this->guardMutable();

        $segments = explode('.', $path);
        $current = &$this->data;

        foreach ($segments as $i => $segment) {
            if ($i === count($segments) - 1) {
                if (is_array($current)) {
                    $current[$segment] = $value;
                } elseif (is_object($current)) {
                    $current->$segment = $value;
                } else {
                    if (is_numeric($segment)) {
                        $current = [$segment => $value];
                    } else {
                        $current = (object)[$segment => $value];
                    }
                }
            } else {
                if (is_array($current)) {
                    if (!isset($current[$segment]) || (!is_array($current[$segment]) && !is_object($current[$segment]))) {
                        $current[$segment] = [];
                    }
                    $current = &$current[$segment];
                } elseif (is_object($current)) {
                    if (!isset($current->$segment) || (!is_array($current->$segment) && !is_object($current->$segment))) {
                        $current->$segment = new \stdClass();
                    }
                    $current = &$current->$segment;
                } else {
                    if (is_numeric($segment)) {
                        $current = [$segment => []];
                        $current = &$current[$segment];
                    } else {
                        $current = (object)[$segment => new \stdClass()];
                        $current = &$current->$segment;
                    }
                }
            }
        }

        return $this;
     }

     /**
     * Find the first element matching a condition using PHP 8.4+ array_find.
     *
     * @param callable $callback Predicate function: function(mixed $value, string|int $key): bool
     * @param mixed $default Default value if no match found
     * @return mixed The first matching element or default
     */
     public function findElement(callable $callback, mixed $default = null): mixed
     {
        if (!is_array($this->data)) {
            return $default;
        }

        return ArrayHelpers::find($this->data, $callback, $default);
     }

     /**
     * Find the key of the first element matching a condition using PHP 8.4+ array_find_key.
     *
     * @param callable $callback Predicate function: function(mixed $value, string|int $key): bool
     * @param mixed $default Default key if no match found
     * @return string|int|mixed The key of the first matching element or default
     */
     public function findElementKey(callable $callback, mixed $default = null): mixed
     {
        if (!is_array($this->data)) {
            return $default;
        }

        return ArrayHelpers::findKey($this->data, $callback, $default);
     }

     /**
     * Check if ANY element matches a condition using PHP 8.4+ array_any.
     *
     * @param callable $callback Predicate function: function(mixed $value, string|int $key): bool
     * @return bool True if any element matches, false otherwise
     */
     public function anyMatch(callable $callback): bool
     {
        if (!is_array($this->data)) {
            return false;
        }

        return ArrayHelpers::any($this->data, $callback);
     }

     /**
     * Check if ALL elements match a condition using PHP 8.4+ array_all.
     *
     * @param callable $callback Predicate function: function(mixed $value, string|int $key): bool
     * @return bool True if all elements match, false otherwise
     */
     public function allMatch(callable $callback): bool
     {
        if (!is_array($this->data)) {
            return true;
        }

        return ArrayHelpers::all($this->data, $callback);
     }

     /**
     * Get the first element matching a condition or default.
     *
     * @param callable|null $callback Optional predicate function: function(mixed $value, string|int $key): bool
     * @param mixed $default Default value if no match found
     * @return mixed The first matching element or default
     */
     public function firstElement(?callable $callback = null, mixed $default = null): mixed
     {
        if (!is_array($this->data)) {
            return $default;
        }

        return ArrayHelpers::first($this->data, $callback, $default);
     }

     /**
     * Get the last element matching a condition or default.
     *
     * @param callable|null $callback Optional predicate function: function(mixed $value, string|int $key): bool
     * @param mixed $default Default value if no match found
     * @return mixed The last matching element or default
     */
     public function lastElement(?callable $callback = null, mixed $default = null): mixed
     {
        if (!is_array($this->data)) {
            return $default;
        }

        return ArrayHelpers::last($this->data, $callback, $default);
     }

     /**
     * Get the key of the last element matching a condition.
     *
     * @param callable $callback Predicate function: function(mixed $value, string|int $key): bool
     * @param mixed $default Default key if no match found
     * @return string|int|mixed The key of the last matching element or default
     */
     public function lastElementKey(callable $callback, mixed $default = null): mixed
     {
        if (!is_array($this->data)) {
            return $default;
        }

        return ArrayHelpers::lastKey($this->data, $callback, $default);
     }

     /**
     * Check if ANY of the given keys exist in the data.
     *
     * @param array $keys Keys to check
     * @return bool True if any key exists, false otherwise
     */
     public function hasAnyKey(array $keys): bool
     {
        if (!is_array($this->data)) {
            return false;
        }

        return ArrayHelpers::hasAny($this->data, $keys);
     }

     /**
     * Check if ALL of the given keys exist in the data.
     *
     * @param array $keys Keys to check
     * @return bool True if all keys exist, false otherwise
     */
     public function hasAllKeys(array $keys): bool
     {
        if (!is_array($this->data)) {
            return false;
        }

        return ArrayHelpers::hasAll($this->data, $keys);
     }

     /**
     * Map elements with callable support and key handling.
     *
     * @param callable $callback Transformation function: function(mixed $value, string|int $key): mixed
     * @return static New instance with mapped data
     */
     public function mapWith(callable $callback): static
     {
        if (!is_array($this->data)) {
            return $this;
        }

        $mapped = ArrayHelpers::map($this->data, $callback);
        $new = clone $this;
        $new->data = $mapped;
        if (property_exists($new, 'mutabilityMode')) {
            $new->mutabilityMode = JsonMutabilityMode::MUTABLE;
        }

        return $new;
     }

     /**
     * Filter elements with callable support and key handling (alias for array_filter with ARRAY_FILTER_USE_BOTH).
     *
     * @param callable $callback Predicate function: function(mixed $value, string|int $key): bool
     * @return static New instance with filtered data
     */
     public function filterWith(callable $callback): static
     {
        if (!is_array($this->data)) {
            return $this;
        }

        $filtered = ArrayHelpers::filterByCallback($this->data, $callback);
        $new = clone $this;
        $new->data = $filtered;
        if (property_exists($new, 'mutabilityMode')) {
            $new->mutabilityMode = JsonMutabilityMode::MUTABLE;
        }

        return $new;
    }
}
