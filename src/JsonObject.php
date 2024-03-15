<?php

namespace Skpassegna\JsonParser;

use Skpassegna\JsonParser\Exceptions\JsonKeyNotFoundException;
use Skpassegna\JsonParser\Contracts\JsonIterable;
use Skpassegna\JsonParser\Contracts\JsonAccessible;

class JsonObject implements JsonAccessible, JsonIterable
{
    /**
     * @var array The underlying data for the JSON object.
     */
    protected $data;

    /**
     * JsonObject constructor.
     *
     * @param array $data The data for the JSON object.
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Get a property value by key.
     *
     * @param string $key The property key.
     * @return mixed The property value.
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    /**
     * Set a property value by key.
     *
     * @param string $key The property key.
     * @param mixed $value The property value.
     */
    public function __set(string $key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Check if a property exists by key.
     *
     * @param string $key The property key.
     * @return bool True if the property exists, false otherwise.
     */
    public function __isset(string $key): bool
    {
        return $this->has($key);
    }

    /**
     * Unset a property by key.
     *
     * @param string $key The property key.
     */
    public function __unset(string $key)
    {
        $this->remove($key);
    }

    /**
     * Get a property value by key.
     *
     * @param string $key The property key.
     * @param mixed $default The default value to return if the property doesn't exist.
     * @return mixed The property value, or the default value if the property doesn't exist.
     */
    public function get(string $key, $default = null)
    {
        if (str_contains($key, '.')) {
            return $this->getNestedValue($key, $default);
        }

        if (!array_key_exists($key, $this->data)) {
            throw new JsonKeyNotFoundException($key);
        }

        return $this->data[$key] ?? $default;
    }

    /**
     * Set a property value by key.
     *
     * @param string $key The property key.
     * @param mixed $value The property value.
     * @return $this
     */
    public function set(string $key, $value): self
    {
        if (str_contains($key, '.')) {
            $this->setNestedValue($key, $value);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Check if a property exists by key.
     *
     * @param string $key The property key.
     * @return bool True if the property exists, false otherwise.
     */
    public function has(string $key): bool
    {
        if (str_contains($key, '.')) {
            return $this->hasNestedValue($key);
        }

        return array_key_exists($key, $this->data);
    }

    /**
     * Remove a property by key.
     *
     * @param string $key The property key.
     * @return $this
     */
    public function remove(string $key): self
    {
        if (str_contains($key, '.')) {
            $this->removeNestedValue($key);
        } else {
            if (!array_key_exists($key, $this->data)) {
                throw new JsonKeyNotFoundException($key);
            }

            unset($this->data[$key]);
        }

        return $this;
    }

    /**
     * Get all properties as an array.
     *
     * @return array The properties array.
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Convert the object to a JSON string.
     *
     * @return string The JSON string representation of the object.
     */
    public function toJson(): string
    {
        return json_encode($this->data);
    }

    /**
     * Get an iterator for the object properties.
     *
     * @return \Traversable An iterator for the object properties.
     */
    public function getIterator(): \Traversable
    {
        return new JsonObjectIterator($this->data);
    }

    /**
     * Check if the offset exists in the object.
     *
     * @param mixed $offset The offset to check.
     * @return bool True if the offset exists, false otherwise.
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * Get the value at the specified offset.
     *
     * @param mixed $offset The offset to retrieve.
     * @return mixed The value at the specified offset.
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Set the value at the specified offset.
     *
     * @param mixed $offset The offset to set.
     * @param mixed $value The value to set.
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * Unset the value at the specified offset.
     *
     * @param mixed $offset The offset to unset.
     */
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    /**
     * Get a nested value by key path.
     *
     * @param string $keyPath The key path (e.g., 'parent.child.grandchild').
     * @param mixed $default The default value to return if the key path doesn't exist.
     * @return mixed The nested value, or the default value if the key path doesn't exist.
     */
    private function getNestedValue(string $keyPath, $default = null)
    {
        $keys = explode('.', $keyPath);
        $value = $this->data;

        foreach ($keys as $key) {
            if (!is_array($value) && !($value instanceof JsonObject)) {
                return $default;
            }

            if (is_array($value)) {
                $value = $value[$key] ?? null;
            } else {
                $value = $value->get($key);
            }
        }

        return $value ?? $default;
    }

    /**
     * Set a nested value by key path.
     *
     * @param string $keyPath The key path (e.g., 'parent.child.grandchild').
     * @param mixed $value The value to set.
     */
    private function setNestedValue(string $keyPath, $value): void
    {
        $keys = explode('.', $keyPath);
        $lastKey = array_pop($keys);
        $ref = &$this->data;

        foreach ($keys as $key) {
            if (!isset($ref[$key]) || !is_array($ref[$key])) {
                $ref[$key] = [];
            }

            $ref = &$ref[$key];
        }

        $ref[$lastKey] = $value;
    }

    /**
     * Check if a nested value exists by key path.
     *
     * @param string $keyPath The key path (e.g., 'parent.child.grandchild').
     * @return bool True if the nested value exists, false otherwise.
     */
    private function hasNestedValue(string $keyPath): bool
    {
        $keys = explode('.', $keyPath);
        $value = $this->data;

        foreach ($keys as $key) {
            if (!is_array($value) && !($value instanceof JsonObject)) {
                return false;
            }

            if (is_array($value)) {
                if (!array_key_exists($key, $value)) {
                    return false;
                }

                $value = $value[$key];
            } else {
                if (!$value->has($key)) {
                    return false;
                }

                $value = $value->get($key);
            }
        }

        return true;
    }

    /**
     * Remove a nested value by key path.
     *
     * @param string $keyPath The key path (e.g., 'parent.child.grandchild').
     */
    private function removeNestedValue(string $keyPath): void
    {
        $keys = explode('.', $keyPath);
        $lastKey = array_pop($keys);
        $ref = &$this->data;

        foreach ($keys as $key) {
            if (!isset($ref[$key]) || !is_array($ref[$key])) {
                return;
            }

            $ref = &$ref[$key];
        }

        unset($ref[$lastKey]);
    }
}
