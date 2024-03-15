<?php

namespace Skpassegna\JsonParser;

use Skpassegna\JsonParser\Exceptions\JsonKeyNotFoundException;

class JsonHelper
{
    /**
     * Check if a value exists in an object or array by key path.
     *
     * @param JsonObject|JsonArray $data The object or array to check.
     * @param string $keyPath The key path (e.g., 'parent.child.grandchild').
     * @return bool True if the value exists, false otherwise.
     */
    public static function has($data, string $keyPath): bool
    {
        if ($data instanceof JsonObject) {
            return $data->has($keyPath);
        } elseif ($data instanceof JsonArray) {
            $keys = explode('.', $keyPath);
            $key = array_shift($keys);

            if (!isset($data[$key])) {
                throw new JsonKeyNotFoundException($key);
            }

            $value = $data[$key];

            if (empty($keys)) {
                return true;
            }

            $keyPath = implode('.', $keys);

            if ($value instanceof JsonObject) {
                return $value->has($keyPath);
            } elseif ($value instanceof JsonArray) {
                return static::has($value, $keyPath);
            }
        }

        throw new JsonException('Invalid data type for has operation.');
    }



    /**
     * Get a value from an object or array by key path.
     *
     * @param JsonObject|JsonArray $data The object or array to get the value from.
     * @param string $keyPath The key path (e.g., 'parent.child.grandchild').
     * @param mixed $default The default value to return if the key path doesn't exist.
     * @return mixed The value at the specified key path, or the default value if it doesn't exist.
     */
    public static function get($data, string $keyPath, $default = null)
    {
        if ($data instanceof JsonObject) {
            return $data->get($keyPath, $default);
        } elseif ($data instanceof JsonArray) {
            $keys = explode('.', $keyPath);
            $key = array_shift($keys);

            if (!isset($data[$key])) {
                throw new JsonKeyNotFoundException($key);
            }

            $value = $data[$key];

            if (empty($keys)) {
                return $value;
            }

            $keyPath = implode('.', $keys);

            if ($value instanceof JsonObject) {
                return $value->get($keyPath, $default);
            } elseif ($value instanceof JsonArray) {
                return static::get($value, $keyPath, $default);
            }
        }

        throw new JsonException('Invalid data type for get operation.');
    }
}
