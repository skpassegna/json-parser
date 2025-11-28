<?php

declare(strict_types=1);

namespace Skpassegna\Json\Utils;

final class ArrayHelpers
{
    public static function find(array $array, callable $callback, mixed $default = null): mixed
    {
        if (function_exists('array_find')) {
            return array_find($array, $callback) ?? $default;
        }

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    public static function findKey(array $array, callable $callback, mixed $default = null): mixed
    {
        if (function_exists('array_find_key')) {
            return array_find_key($array, $callback) ?? $default;
        }

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $key;
            }
        }

        return $default;
    }

    public static function any(array $array, callable $callback): bool
    {
        if (function_exists('array_any')) {
            return array_any($array, $callback);
        }

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return true;
            }
        }

        return false;
    }

    public static function all(array $array, callable $callback): bool
    {
        if (function_exists('array_all')) {
            return array_all($array, $callback);
        }

        foreach ($array as $key => $value) {
            if (!$callback($value, $key)) {
                return false;
            }
        }

        return true;
    }

    public static function first(array $array, callable|null $callback = null, mixed $default = null): mixed
    {
        if (empty($array)) {
            return $default;
        }

        if ($callback === null) {
            return reset($array);
        }

        return self::find($array, $callback, $default);
    }

    public static function last(array $array, callable|null $callback = null, mixed $default = null): mixed
    {
        if (empty($array)) {
            return $default;
        }

        if ($callback === null) {
            return end($array) ?: $default;
        }

        $result = $default;
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                $result = $value;
            }
        }

        return $result;
    }

    public static function lastKey(array $array, callable $callback, mixed $default = null): mixed
    {
        $lastKey = $default;

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                $lastKey = $key;
            }
        }

        return $lastKey;
    }

    public static function map(array $array, callable $callback): array
    {
        $results = [];
        foreach ($array as $key => $value) {
            $results[$key] = $callback($value, $key);
        }
        return $results;
    }

    public static function filterByCallback(array $array, callable $callback): array
    {
        $results = [];
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                $results[$key] = $value;
            }
        }
        return $results;
    }

    public static function reduce(array $array, callable $callback, mixed $initial = null): mixed
    {
        return array_reduce($array, $callback, $initial);
    }

    public static function hasAny(array $array, array $keys): bool
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                return true;
            }
        }
        return false;
    }

    public static function hasAll(array $array, array $keys): bool
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $array)) {
                return false;
            }
        }
        return true;
    }
}
