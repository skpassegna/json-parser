<?php

namespace Skpassegna\JsonParser\Contracts;

interface JsonAccessible
{
    /**
     * Check if the offset exists in the object.
     *
     * @param mixed $offset The offset to check.
     * @return bool True if the offset exists, false otherwise.
     */
    public function offsetExists($offset): bool;

    /**
     * Get the value at the specified offset.
     *
     * @param mixed $offset The offset to retrieve.
     * @return mixed The value at the specified offset.
     */
    public function offsetGet($offset);

    /**
     * Set the value at the specified offset.
     *
     * @param mixed $offset The offset to set.
     * @param mixed $value The value to set.
     */
    public function offsetSet($offset, $value): void;

    /**
     * Unset the value at the specified offset.
     *
     * @param mixed $offset The offset to unset.
     */
    public function offsetUnset($offset): void;
}