<?php

declare(strict_types=1);

namespace Skpassegna\Json\Traits;

trait DataAccessTrait
{
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
        return is_array($this->data) ? count($this->data) : 0;
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
}
