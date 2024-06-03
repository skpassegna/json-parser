<?php

namespace Skpassegna\JsonParser;

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
        $keys = explode('.', $keyPath);
        $currentData = $data;

        foreach ($keys as $key) {
            if ($currentData instanceof JsonObject) {
                if (!$currentData->has($key)) {
                    return false;
                }
                $currentData = $currentData->get($key);
            } elseif ($currentData instanceof JsonArray) {
                if (!isset($currentData[$key])) {
                    return false;
                }
                $currentData = $currentData[$key];
            } else {
                return false;
            }
        }

        return true;
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
        $keys = explode('.', $keyPath);
        $currentData = $data;

        foreach ($keys as $key) {
            if ($currentData instanceof JsonObject) {
                if (!$currentData->has($key)) {
                    return $default;
                }
                $currentData = $currentData->get($key);
            } elseif ($currentData instanceof JsonArray) {
                if (!isset($currentData[$key])) {
                    return $default;
                }
                $currentData = $currentData[$key];
            } else {
                return $default;
            }
        }

        return $currentData;
    }
}
