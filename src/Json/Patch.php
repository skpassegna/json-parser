<?php

declare(strict_types=1);

namespace Skpassegna\Json\Json;

use Skpassegna\Json\Contracts\PatchInterface;
use Skpassegna\Json\Exceptions\RuntimeException;

class Patch implements PatchInterface
{
    private Serializer $serializer;
    private Pointer $pointer;

    public function __construct()
    {
        $this->serializer = new Serializer();
        $this->pointer = new Pointer();
    }

    /**
     * @inheritDoc
     */
    public function apply(string|array $document, string|array $patch, bool $mutate = false): string|array
    {
        try {
            $doc = is_string($document) ? $this->serializer->deserialize($document, true) : $document;
            $operations = is_string($patch) ? $this->serializer->deserialize($patch, true) : $patch;

            if (!is_array($operations)) {
                throw new RuntimeException('Patch must be an array of operations');
            }

            $result = $mutate ? $doc : json_decode(json_encode($doc), true);

            foreach ($operations as $operation) {
                if (!isset($operation['op'], $operation['path'])) {
                    throw new RuntimeException('Invalid patch operation: missing op or path');
                }

                $path = $operation['path'];
                $value = $operation['value'] ?? null;

                switch ($operation['op']) {
                    case 'add':
                        $result = $this->pointer->set($result, $path, $value, true);
                        break;

                    case 'remove':
                        $result = $this->pointer->remove($result, $path, true);
                        break;

                    case 'replace':
                        if (!$this->pointer->has($result, $path)) {
                            throw new RuntimeException("Cannot replace non-existent value at path: $path");
                        }
                        $result = $this->pointer->set($result, $path, $value, true);
                        break;

                    case 'move':
                        if (!isset($operation['from'])) {
                            throw new RuntimeException('Move operation missing "from" path');
                        }
                        $value = $this->pointer->get($result, $operation['from']);
                        $result = $this->pointer->remove($result, $operation['from'], true);
                        $result = $this->pointer->set($result, $path, $value, true);
                        break;

                    case 'copy':
                        if (!isset($operation['from'])) {
                            throw new RuntimeException('Copy operation missing "from" path');
                        }
                        $value = $this->pointer->get($result, $operation['from']);
                        $result = $this->pointer->set($result, $path, $value, true);
                        break;

                    case 'test':
                        $actual = $this->pointer->get($result, $path);
                        if ($actual !== $value) {
                            throw new RuntimeException("Test failed: value at '$path' does not match expected value");
                        }
                        break;

                    default:
                        throw new RuntimeException("Unknown operation: {$operation['op']}");
                }
            }

            return is_string($document) ? $this->serializer->serialize($result) : $result;
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to apply patch: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function diff(string|array $source, string|array $target): string
    {
        try {
            $src = is_string($source) ? $this->serializer->deserialize($source, true) : $source;
            $tgt = is_string($target) ? $this->serializer->deserialize($target, true) : $target;

            $operations = [];
            $this->diffRecursive($src, $tgt, '', $operations);

            return $this->serializer->serialize($operations);
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to generate diff: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function test(string|array $document, string|array $patch): bool
    {
        try {
            $this->apply($document, $patch);
            return true;
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Generate diff operations recursively
     *
     * @param array<mixed> $source Source document
     * @param array<mixed> $target Target document
     * @param string $path Current JSON Pointer path
     * @param array<array<string,mixed>> $operations Accumulated patch operations
     * @return void
     */
    private function diffRecursive(array $source, array $target, string $path, array &$operations): void
    {
        foreach ($target as $key => $value) {
            $pointer = $path . '/' . str_replace('~', '~0', str_replace('/', '~1', (string)$key));

            if (!array_key_exists($key, $source)) {
                $operations[] = ['op' => 'add', 'path' => $pointer, 'value' => $value];
                continue;
            }

            if ($value !== $source[$key]) {
                if (is_array($value) && is_array($source[$key])) {
                    $this->diffRecursive($source[$key], $value, $pointer, $operations);
                } else {
                    $operations[] = ['op' => 'replace', 'path' => $pointer, 'value' => $value];
                }
            }
        }

        foreach ($source as $key => $value) {
            if (!array_key_exists($key, $target)) {
                $pointer = $path . '/' . str_replace('~', '~0', str_replace('/', '~1', (string)$key));
                $operations[] = ['op' => 'remove', 'path' => $pointer];
            }
        }
    }
}
