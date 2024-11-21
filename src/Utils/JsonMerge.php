<?php

declare(strict_types=1);

namespace Skpassegna\Json\Utils;

class JsonMerge
{
    /**
     * Merge strategy constants.
     */
    public const MERGE_REPLACE = 'replace';
    public const MERGE_RECURSIVE = 'recursive';
    public const MERGE_DISTINCT = 'distinct';

    /**
     * Merge two JSON structures using the specified strategy.
     *
     * @param mixed $target The target JSON structure
     * @param mixed $source The source JSON structure to merge
     * @param string $strategy The merge strategy to use (replace, recursive, or distinct)
     * @return mixed The merged result
     */
    public static function merge(mixed $target, mixed $source, string $strategy = self::MERGE_RECURSIVE): mixed
    {
        if (!is_array($target) && !is_object($target)) {
            return $source;
        }

        if (!is_array($source) && !is_object($source)) {
            return $source;
        }

        return match ($strategy) {
            self::MERGE_REPLACE => self::mergeReplace($target, $source),
            self::MERGE_RECURSIVE => self::mergeRecursive($target, $source),
            self::MERGE_DISTINCT => self::mergeDistinct($target, $source),
            default => throw new \InvalidArgumentException("Invalid merge strategy: {$strategy}")
        };
    }

    /**
     * Replace merge strategy - source completely replaces target at each level.
     *
     * @param mixed $target
     * @param mixed $source
     * @return mixed
     */
    private static function mergeReplace(mixed $target, mixed $source): mixed
    {
        return $source;
    }

    /**
     * Recursive merge strategy - deeply merge arrays and objects.
     *
     * @param mixed $target
     * @param mixed $source
     * @return mixed
     */
    private static function mergeRecursive(mixed $target, mixed $source): mixed
    {
        if (is_array($target) && is_array($source)) {
            foreach ($source as $key => $value) {
                if (isset($target[$key]) && (is_array($target[$key]) || is_object($target[$key]))) {
                    $target[$key] = self::mergeRecursive($target[$key], $value);
                } else {
                    $target[$key] = $value;
                }
            }
            return $target;
        }

        if (is_object($target) && is_object($source)) {
            foreach (get_object_vars($source) as $key => $value) {
                if (isset($target->$key) && (is_array($target->$key) || is_object($target->$key))) {
                    $target->$key = self::mergeRecursive($target->$key, $value);
                } else {
                    $target->$key = $value;
                }
            }
            return $target;
        }

        return $source;
    }

    /**
     * Distinct merge strategy - only add new keys, never override existing ones.
     *
     * @param mixed $target
     * @param mixed $source
     * @return mixed
     */
    private static function mergeDistinct(mixed $target, mixed $source): mixed
    {
        if (is_array($target) && is_array($source)) {
            foreach ($source as $key => $value) {
                if (!array_key_exists($key, $target)) {
                    $target[$key] = $value;
                } elseif (is_array($target[$key]) || is_object($target[$key])) {
                    $target[$key] = self::mergeDistinct($target[$key], $value);
                }
            }
            return $target;
        }

        if (is_object($target) && is_object($source)) {
            foreach (get_object_vars($source) as $key => $value) {
                if (!property_exists($target, $key)) {
                    $target->$key = $value;
                } elseif (is_array($target->$key) || is_object($target->$key)) {
                    $target->$key = self::mergeDistinct($target->$key, $value);
                }
            }
            return $target;
        }

        return $target;
    }
}
