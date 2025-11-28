<?php

declare(strict_types=1);

namespace Skpassegna\Json\Utils;

use Skpassegna\Json\Enums\DiffMergeStrategy;

/**
 * Advanced diff and merge strategy implementations.
 *
 * Provides RFC 6902/7396 compliant strategies and conflict detection
 * for sophisticated JSON document manipulation.
 */
final class DiffMergeStrategies
{
    /**
     * Get a merge strategy as a callable.
     *
     * @param DiffMergeStrategy $strategy
     * @return callable
     */
    public static function getMergeStrategy(DiffMergeStrategy $strategy): callable
    {
        return match ($strategy) {
            DiffMergeStrategy::MERGE_RECURSIVE => self::mergeRecursive(...),
            DiffMergeStrategy::MERGE_DEEP => self::mergeDeep(...),
            DiffMergeStrategy::MERGE_REPLACE => self::mergeReplace(...),
            DiffMergeStrategy::MERGE_SHALLOW => self::mergeShallow(...),
            DiffMergeStrategy::MERGE_PATCH_RFC7396 => self::mergeRfc7396(...),
            DiffMergeStrategy::MERGE_CONFLICT_AWARE => self::mergeConflictAware(...),
            default => throw new \InvalidArgumentException("Unsupported merge strategy: {$strategy->value}"),
        };
    }

    /**
     * Get a diff strategy as a callable.
     *
     * @param DiffMergeStrategy $strategy
     * @return callable
     */
    public static function getDiffStrategy(DiffMergeStrategy $strategy): callable
    {
        return match ($strategy) {
            DiffMergeStrategy::DIFF_STRUCTURAL => self::diffStructural(...),
            DiffMergeStrategy::DIFF_RFC6902_PATCH => self::diffRfc6902(...),
            DiffMergeStrategy::DIFF_DETAILED => self::diffDetailed(...),
            DiffMergeStrategy::DIFF_SUMMARY => self::diffSummary(...),
            default => throw new \InvalidArgumentException("Unsupported diff strategy: {$strategy->value}"),
        };
    }

    /**
     * Recursive merge: deeply merge nested structures.
     *
     * @param mixed $target
     * @param mixed $source
     * @return mixed
     */
    public static function mergeRecursive(mixed $target, mixed $source): mixed
    {
        if (!is_array($target) && !is_object($target)) {
            return $source;
        }

        if (!is_array($source) && !is_object($source)) {
            return $source;
        }

        $target = (array)$target;
        $source = (array)$source;

        foreach ($source as $key => $value) {
            if (isset($target[$key]) && (is_array($target[$key]) || is_object($target[$key]))) {
                $target[$key] = self::mergeRecursive($target[$key], $value);
            } else {
                $target[$key] = $value;
            }
        }

        return $target;
    }

    /**
     * Deep merge: recursive with array value merging.
     *
     * @param mixed $target
     * @param mixed $source
     * @return mixed
     */
    public static function mergeDeep(mixed $target, mixed $source): mixed
    {
        $target = (array)$target;
        $source = (array)$source;

        foreach ($source as $key => $value) {
            if (isset($target[$key])) {
                if (is_array($target[$key]) && is_array($value)) {
                    $target[$key] = array_merge($target[$key], self::mergeDeep($target[$key], $value));
                } else {
                    $target[$key] = self::mergeDeep($target[$key], $value);
                }
            } else {
                $target[$key] = $value;
            }
        }

        return $target;
    }

    /**
     * Replace merge: source completely replaces target.
     *
     * @param mixed $target
     * @param mixed $source
     * @return mixed
     */
    public static function mergeReplace(mixed $target, mixed $source): mixed
    {
        return $source;
    }

    /**
     * Shallow merge: only merge top level, don't recurse.
     *
     * @param mixed $target
     * @param mixed $source
     * @return mixed
     */
    public static function mergeShallow(mixed $target, mixed $source): mixed
    {
        if (!is_array($target) && !is_object($target)) {
            return $source;
        }

        $target = (array)$target;
        $source = (array)$source;

        return array_merge($target, $source);
    }

    /**
     * RFC 7396 JSON Merge Patch: null values remove keys, non-objects replace.
     *
     * @param mixed $target
     * @param mixed $source
     * @return mixed
     */
    public static function mergeRfc7396(mixed $target, mixed $source): mixed
    {
        if (!is_array($source) && !is_object($source)) {
            return $source;
        }

        if (!is_array($target) && !is_object($target)) {
            $target = [];
        }

        $target = (array)$target;
        $source = (array)$source;

        foreach ($source as $key => $value) {
            if (is_null($value)) {
                unset($target[$key]);
            } elseif (isset($target[$key]) && (is_array($target[$key]) || is_object($target[$key])) && (is_array($value) || is_object($value))) {
                $target[$key] = self::mergeRfc7396($target[$key], $value);
            } else {
                $target[$key] = $value;
            }
        }

        return $target;
    }

    /**
     * Conflict-aware merge: detect and report conflicts.
     *
     * @param mixed $target
     * @param mixed $source
     * @param mixed $base Optional base for three-way merge
     * @return array{result: mixed, conflicts: array<string, array{path: string, target: mixed, source: mixed}>}
     */
    public static function mergeConflictAware(
        mixed $target,
        mixed $source,
        mixed $base = null
    ): array {
        $conflicts = [];
        $result = self::mergeWithConflictDetection($target, $source, $base, $conflicts);

        return [
            'result' => $result,
            'conflicts' => $conflicts,
        ];
    }

    /**
     * Structural diff: identify added, removed, modified keys.
     *
     * @param mixed $source
     * @param mixed $target
     * @return array{added: array, removed: array, modified: array}
     */
    public static function diffStructural(mixed $source, mixed $target): array
    {
        $source = (array)$source;
        $target = (array)$target;

        return [
            'added' => array_keys(array_diff_key($target, $source)),
            'removed' => array_keys(array_diff_key($source, $target)),
            'modified' => self::findModifiedKeys($source, $target),
        ];
    }

    /**
     * RFC 6902 JSON Patch diff: generate patch operations.
     *
     * @param mixed $source
     * @param mixed $target
     * @return array<int, array{op: string, path: string, value?: mixed, from?: string}>
     */
    public static function diffRfc6902(mixed $source, mixed $target): array
    {
        $patches = [];
        self::generatePatches($source, $target, $patches, '');

        return $patches;
    }

    /**
     * Detailed diff: comprehensive change tracking.
     *
     * @param mixed $source
     * @param mixed $target
     * @return array<string, mixed>
     */
    public static function diffDetailed(mixed $source, mixed $target): array
    {
        $structural = self::diffStructural($source, $target);
        $patches = self::diffRfc6902($source, $target);

        return [
            'structural' => $structural,
            'patches' => $patches,
            'equality' => $source === $target,
            'similarity' => self::calculateSimilarity($source, $target),
        ];
    }

    /**
     * Summary diff: quick overview of changes.
     *
     * @param mixed $source
     * @param mixed $target
     * @return array<string, int>
     */
    public static function diffSummary(mixed $source, mixed $target): array
    {
        $structural = self::diffStructural($source, $target);

        return [
            'added_count' => count($structural['added']),
            'removed_count' => count($structural['removed']),
            'modified_count' => count($structural['modified']),
            'total_changes' => count($structural['added']) + count($structural['removed']) + count($structural['modified']),
            'equal' => $source === $target,
        ];
    }

    // Private helper methods

    private static function mergeWithConflictDetection(
        mixed $target,
        mixed $source,
        mixed $base,
        array &$conflicts,
        string $path = ''
    ): mixed {
        if (!is_array($target) && !is_object($target)) {
            return $source;
        }

        $target = (array)$target;
        $source = (array)$source;
        $base = is_array($base) || is_object($base) ? (array)$base : [];

        foreach ($source as $key => $value) {
            $currentPath = $path ? "{$path}/{$key}" : $key;

            if (isset($target[$key])) {
                $baseValue = $base[$key] ?? null;

                if (is_array($target[$key]) && is_array($value)) {
                    $target[$key] = self::mergeWithConflictDetection($target[$key], $value, $baseValue, $conflicts, $currentPath);
                } elseif ($target[$key] !== $value) {
                    if ($base !== [] && isset($base[$key]) && $target[$key] !== $baseValue && $value !== $baseValue) {
                        $conflicts[$currentPath] = [
                            'path' => $currentPath,
                            'target' => $target[$key],
                            'source' => $value,
                            'base' => $baseValue,
                        ];
                    }
                    $target[$key] = $value;
                }
            } else {
                $target[$key] = $value;
            }
        }

        return $target;
    }

    private static function findModifiedKeys(array $source, array $target): array
    {
        $modified = [];

        foreach ($source as $key => $value) {
            if (isset($target[$key])) {
                if (is_array($value) && is_array($target[$key])) {
                    if ($value !== $target[$key]) {
                        $modified[] = $key;
                    }
                } elseif ($value !== $target[$key]) {
                    $modified[] = $key;
                }
            }
        }

        return $modified;
    }

    private static function generatePatches(mixed $source, mixed $target, array &$patches, string $path = ''): void
    {
        if ($source === $target) {
            return;
        }

        if (!is_array($source) && !is_object($source)) {
            $patches[] = [
                'op' => 'replace',
                'path' => $path ?: '/',
                'value' => $target,
            ];
            return;
        }

        $source = (array)$source;
        $target = (array)$target;

        foreach (array_diff_key($source, $target) as $key => $value) {
            $patches[] = [
                'op' => 'remove',
                'path' => ($path ?: '') . '/' . self::encodePath($key),
            ];
        }

        foreach ($target as $key => $value) {
            $keyPath = ($path ?: '') . '/' . self::encodePath($key);

            if (!isset($source[$key])) {
                $patches[] = [
                    'op' => 'add',
                    'path' => $keyPath,
                    'value' => $value,
                ];
            } else {
                self::generatePatches($source[$key], $value, $patches, $keyPath);
            }
        }
    }

    private static function encodePath(string|int $key): string
    {
        $key = (string)$key;
        return strtr($key, [
            '~' => '~0',
            '/' => '~1',
        ]);
    }

    private static function calculateSimilarity(mixed $source, mixed $target): float
    {
        if ($source === $target) {
            return 1.0;
        }

        $sourceStr = json_encode($source) ?: '';
        $targetStr = json_encode($target) ?: '';

        if ($sourceStr === '' || $targetStr === '') {
            return 0.0;
        }

        similar_text($sourceStr, $targetStr, $percent);

        return $percent / 100.0;
    }
}
