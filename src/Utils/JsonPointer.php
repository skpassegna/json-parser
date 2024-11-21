<?php

declare(strict_types=1);

namespace Skpassegna\Json\Utils;

use Skpassegna\Json\Exceptions\PathException;

class JsonPointer
{
    /**
     * Evaluate a JSON Pointer expression against JSON data.
     *
     * @param mixed $data The JSON data
     * @param string $pointer The JSON Pointer expression
     * @return mixed The value at the pointer location
     * @throws PathException If the pointer is invalid or the path doesn't exist
     */
    public static function get(mixed $data, string $pointer): mixed
    {
        if ($pointer === '') {
            return $data;
        }

        if (!str_starts_with($pointer, '/')) {
            throw new PathException('Invalid JSON Pointer: must start with "/"');
        }

        $segments = self::parsePointer($pointer);
        $current = $data;

        foreach ($segments as $segment) {
            if (is_array($current)) {
                if (!array_key_exists($segment, $current)) {
                    throw new PathException("Path not found: {$pointer}");
                }
                $current = $current[$segment];
            } elseif (is_object($current)) {
                if (!property_exists($current, $segment)) {
                    throw new PathException("Path not found: {$pointer}");
                }
                $current = $current->$segment;
            } else {
                throw new PathException("Cannot traverse further: value at path is neither array nor object");
            }
        }

        return $current;
    }

    /**
     * Set a value at the specified JSON Pointer location.
     *
     * @param mixed $data The JSON data to modify
     * @param string $pointer The JSON Pointer expression
     * @param mixed $value The value to set
     * @return mixed The modified data
     * @throws PathException If the pointer is invalid or the path cannot be created
     */
    public static function set(mixed &$data, string $pointer, mixed $value): mixed
    {
        if ($pointer === '') {
            return $data = $value;
        }

        if (!str_starts_with($pointer, '/')) {
            throw new PathException('Invalid JSON Pointer: must start with "/"');
        }

        $segments = self::parsePointer($pointer);
        $current = &$data;

        foreach ($segments as $i => $segment) {
            if ($i === count($segments) - 1) {
                if (is_array($current)) {
                    $current[$segment] = $value;
                } elseif (is_object($current)) {
                    $current->$segment = $value;
                } else {
                    throw new PathException("Cannot set value: parent is neither array nor object");
                }
            } else {
                if (is_array($current)) {
                    if (!array_key_exists($segment, $current)) {
                        $current[$segment] = [];
                    }
                    $current = &$current[$segment];
                } elseif (is_object($current)) {
                    if (!property_exists($current, $segment)) {
                        $current->$segment = new \stdClass();
                    }
                    $current = &$current->$segment;
                } else {
                    throw new PathException("Cannot traverse further: value at path is neither array nor object");
                }
            }
        }

        return $data;
    }

    /**
     * Parse a JSON Pointer into path segments.
     *
     * @param string $pointer
     * @return array<string>
     */
    private static function parsePointer(string $pointer): array
    {
        $segments = explode('/', substr($pointer, 1));
        return array_map(function ($segment) {
            return strtr($segment, [
                '~1' => '/',
                '~0' => '~'
            ]);
        }, $segments);
    }
}
