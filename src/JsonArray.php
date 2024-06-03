<?php

namespace Skpassegna\JsonParser;

use Skpassegna\JsonParser\Contracts\JsonIterable;
use Skpassegna\JsonParser\Contracts\JsonAccessible;

class JsonArray extends JsonObject implements JsonAccessible, JsonIterable
{
    /**
     * Filter the array elements by a callback function.
     *
     * @param callable $callback The callback function to filter the elements.
     * @return static A new JsonArray instance with the filtered elements.
     */
    public function filter(callable $callback): self
    {
        $filtered = array_filter($this->data, $callback);
        return new static($filtered);
    }

    /**
     * Map the array elements to a new array using a callback function.
     *
     * @param callable $callback The callback function to apply to each element.
     * @return static A new JsonArray instance with the mapped elements.
     */
    public function map(callable $callback): self
    {
        $mapped = array_map($callback, $this->data);
        return new static($mapped);
    }

    /**
     * Sort the array elements using a user-defined comparison function.
     *
     * @param callable|null $callback The optional comparison function.
     * @return static A new JsonArray instance with the sorted elements.
     */
    public function sort(callable $callback = null): self
    {
        $sorted = $this->data;

        if ($callback !== null) {
            usort($sorted, $callback);
        } else {
            sort($sorted);
        }

        return new static($sorted);
    }

    /**
     * Get the number of elements in the array.
     *
     * @return int The number of elements in the array.
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Merge this array with another JSON array.
     *
     * @param JsonArray $other The other JSON array to merge.
     * @return JsonArray The merged JSON array.
     */
    public function merge(JsonArray $other): JsonArray
    {
        $merged = clone $this;
        foreach ($other as $value) {
            $merged->data[] = $value;
        }
        return $merged;
    }

    /**
     * Check if this array is equal to another JSON array.
     *
     * @param JsonArray $other The other JSON array to compare.
     * @return bool True if the arrays are equal, false otherwise.
     */
    public function equals(JsonArray $other): bool
    {
        if (count($this->data) !== count($other->data)) {
            return false;
        }

        foreach ($this->data as $key => $value) {
            $otherValue = $other->data[$key];
            if ($value instanceof JsonObject && $otherValue instanceof JsonObject) {
                if (!$value->equals($otherValue)) {
                    return false;
                }
            } elseif ($value instanceof JsonArray && $otherValue instanceof JsonArray) {
                if (!$value->equals($otherValue)) {
                    return false;
                }
            } else {
                if ($value !== $otherValue) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Transform this array by applying a callback function to each value.
     *
     * @param callable $callback The callback function to apply.
     * @return JsonArray The transformed JSON array.
     */
    public function transform(callable $callback): JsonArray
    {
        $transformed = new JsonArray();
        foreach ($this->data as $value) {
            if ($value instanceof JsonObject) {
                $transformed->data[] = $value->transform($callback);
            } elseif ($value instanceof JsonArray) {
                $transformed->data[] = $value->transform($callback);
            } else {
                $transformed->data[] = $callback($value);
            }
        }
        return $transformed;
    }
}
