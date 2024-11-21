<?php

declare(strict_types=1);

namespace Skpassegna\Json\Json;

use Skpassegna\Json\Contracts\MergePatchInterface;
use Skpassegna\Json\Exceptions\RuntimeException;

/**
 * Implementation of JSON Merge Patch (RFC 7396)
 * Allows for partial updates to JSON documents
 */
class MergePatch implements MergePatchInterface
{
    private Serializer $serializer;

    public function __construct()
    {
        $this->serializer = new Serializer();
    }

    /**
     * Apply a JSON Merge Patch to a target document
     *
     * @param string|array<mixed> $target The target document
     * @param string|array<mixed> $patch The merge patch to apply
     * @param bool $mutate Whether to mutate the target document
     * @return string|array<mixed> The patched document
     * @throws RuntimeException If the patch cannot be applied
     */
    public function apply(string|array $target, string|array $patch, bool $mutate = false): string|array
    {
        try {
            $targetData = is_string($target) ? $this->serializer->deserialize($target, true) : $target;
            $patchData = is_string($patch) ? $this->serializer->deserialize($patch, true) : $patch;

            $result = $mutate ? $targetData : json_decode(json_encode($targetData), true);
            $result = $this->mergePatch($result, $patchData);

            return is_string($target) ? $this->serializer->serialize($result) : $result;
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to apply merge patch: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Generate a JSON Merge Patch that transforms the source into the target
     *
     * @param string|array<mixed> $source The source document
     * @param string|array<mixed> $target The target document
     * @return string The generated merge patch
     * @throws RuntimeException If the patch cannot be generated
     */
    public function diff(string|array $source, string|array $target): string
    {
        try {
            $sourceData = is_string($source) ? $this->serializer->deserialize($source, true) : $source;
            $targetData = is_string($target) ? $this->serializer->deserialize($target, true) : $target;

            $patch = $this->generateMergePatch($sourceData, $targetData);
            return $this->serializer->serialize($patch);
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to generate merge patch: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Recursively merge patch data into target data according to RFC 7396
     *
     * @param mixed $target The target value
     * @param mixed $patch The patch value
     * @return mixed The merged result
     */
    private function mergePatch(mixed $target, mixed $patch): mixed
    {
        if (!is_array($patch)) {
            return $patch;
        }

        if (!is_array($target)) {
            $target = [];
        }

        $result = $target;

        foreach ($patch as $key => $value) {
            if ($value === null) {
                unset($result[$key]);
            } else {
                $result[$key] = array_key_exists($key, $target) 
                    ? $this->mergePatch($target[$key], $value)
                    : $value;
            }
        }

        return $result;
    }

    /**
     * Generate a merge patch that transforms source into target
     *
     * @param mixed $source The source value
     * @param mixed $target The target value
     * @return mixed The generated patch
     */
    private function generateMergePatch(mixed $source, mixed $target): mixed
    {
        if (!is_array($source) || !is_array($target)) {
            return $target;
        }

        $patch = [];

        // Handle removed or changed values
        foreach ($source as $key => $value) {
            if (!array_key_exists($key, $target)) {
                $patch[$key] = null;
            } elseif ($value !== $target[$key]) {
                $patch[$key] = $this->generateMergePatch($value, $target[$key]);
            }
        }

        // Handle new values
        foreach ($target as $key => $value) {
            if (!array_key_exists($key, $source)) {
                $patch[$key] = $value;
            }
        }

        return $patch;
    }
}
