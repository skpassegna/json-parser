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
}
