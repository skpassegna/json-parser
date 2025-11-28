<?php

declare(strict_types=1);

namespace Skpassegna\Json\Utils;

use Skpassegna\Json\Contracts\ReflectionInspectorInterface;
use Skpassegna\Json\Contracts\EventDispatcherInterface;
use Skpassegna\Json\Contracts\JsonEventInterface;

/**
 * Reflection inspector for comprehensive JSON document introspection.
 *
 * Provides metadata about JSON structures including schema inference,
 * node information, listener details, and structure statistics.
 */
final class ReflectionInspector implements ReflectionInspectorInterface
{
    private array $nodeMetadataCache = [];

    public function __construct(
        private readonly mixed $data,
        private readonly ?EventDispatcherInterface $dispatcher = null,
    ) {
    }

    public function describeDocument(): array
    {
        return [
            'type' => $this->getDataType($this->data),
            'size' => $this->calculateSize(),
            'depth' => $this->calculateDepth(),
            'root_type' => is_array($this->data) ? 'array' : (is_object($this->data) ? 'object' : gettype($this->data)),
            'properties_count' => count((array)$this->data),
            'is_associative' => $this->isAssociativeArray($this->data),
            'has_numeric_keys' => $this->hasNumericKeys($this->data),
        ];
    }

    public function inferSchema(): array
    {
        $schema = [
            'type' => $this->getSchemaType($this->data),
            'properties' => [],
            'required' => [],
        ];

        if (is_array($this->data) || is_object($this->data)) {
            $data = (array)$this->data;

            foreach ($data as $key => $value) {
                $schema['properties'][$key] = $this->inferPropertySchema($value);
                if (!is_null($value)) {
                    $schema['required'][] = $key;
                }
            }
        }

        return $schema;
    }

    public function getNodeMetadata(string $path): array
    {
        if (isset($this->nodeMetadataCache[$path])) {
            return $this->nodeMetadataCache[$path];
        }

        $metadata = [
            'path' => $path,
            'exists' => true,
            'type' => 'unknown',
            'value_type' => 'unknown',
            'parent_type' => 'unknown',
        ];

        try {
            $value = JsonPointer::get($this->data, $path);
            $metadata['exists'] = true;
            $metadata['type'] = gettype($value);
            $metadata['value_type'] = $this->getDataType($value);

            if (is_array($value) || is_object($value)) {
                $metadata['item_count'] = count((array)$value);
            }

            $parentPath = $this->getParentPath($path);
            if ($parentPath !== null) {
                try {
                    $parent = JsonPointer::get($this->data, $parentPath);
                    $metadata['parent_type'] = gettype($parent);
                } catch (\Exception) {
                    $metadata['parent_type'] = 'unknown';
                }
            }
        } catch (\Exception $e) {
            $metadata['exists'] = false;
            $metadata['error'] = $e->getMessage();
        }

        $this->nodeMetadataCache[$path] = $metadata;

        return $metadata;
    }

    public function getListenerInfo(): array
    {
        if ($this->dispatcher === null) {
            return [];
        }

        $info = [];

        foreach ($this->dispatcher->getEventTypes() as $eventType) {
            $listeners = $this->dispatcher->getListeners($eventType);
            $info[$eventType] = [];

            foreach ($listeners as $listener) {
                $info[$eventType][] = $this->getCallableSignature($listener);
            }
        }

        return $info;
    }

    public function getStructureStats(): array
    {
        return [
            'total_keys' => $this->countKeys($this->data),
            'total_nodes' => $this->countNodes($this->data),
            'depth' => $this->calculateDepth(),
            'leaf_count' => $this->countLeaves($this->data),
            'array_count' => $this->countArrays($this->data),
            'object_count' => $this->countObjects($this->data),
            'null_count' => $this->countNulls($this->data),
            'scalar_count' => $this->countScalars($this->data),
        ];
    }

    public function dump(bool $includeValues = false): string
    {
        $output = "JSON Document Reflection\n";
        $output .= str_repeat("=", 40) . "\n\n";

        $output .= "Document Description:\n";
        $description = $this->describeDocument();
        foreach ($description as $key => $value) {
            $output .= sprintf("  %-20s: %s\n", ucfirst(str_replace('_', ' ', $key)), $this->formatValue($value));
        }

        $output .= "\nStructure Statistics:\n";
        $stats = $this->getStructureStats();
        foreach ($stats as $key => $value) {
            $output .= sprintf("  %-20s: %s\n", ucfirst(str_replace('_', ' ', $key)), $value);
        }

        $output .= "\nInferred Schema:\n";
        $schema = $this->inferSchema();
        $output .= $this->formatSchema($schema, 2);

        if ($this->dispatcher !== null) {
            $output .= "\nRegistered Event Listeners:\n";
            $listeners = $this->getListenerInfo();
            if (empty($listeners)) {
                $output .= "  No listeners registered\n";
            } else {
                foreach ($listeners as $eventType => $callables) {
                    $output .= sprintf("  %s (%d listener%s)\n", $eventType, count($callables), count($callables) !== 1 ? 's' : '');
                }
            }
        }

        return $output;
    }

    public function toArray(): array
    {
        return [
            'description' => $this->describeDocument(),
            'schema' => $this->inferSchema(),
            'statistics' => $this->getStructureStats(),
            'listeners' => $this->getListenerInfo(),
        ];
    }

    // Private helper methods

    private function getDataType(mixed $value): string
    {
        return match (true) {
            is_null($value) => 'null',
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_float($value) => 'float',
            is_string($value) => 'string',
            is_array($value) => 'array',
            is_object($value) => 'object',
            default => 'unknown',
        };
    }

    private function getSchemaType(mixed $value): string
    {
        return match (true) {
            is_null($value) => 'null',
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_float($value) => 'number',
            is_string($value) => 'string',
            is_array($value) => $this->isAssociativeArray($value) ? 'object' : 'array',
            is_object($value) => 'object',
            default => 'unknown',
        };
    }

    private function inferPropertySchema(mixed $value): array
    {
        $schema = ['type' => $this->getSchemaType($value)];

        if (is_array($value) && !$this->isAssociativeArray($value)) {
            $schema['items'] = $this->inferItemSchema($value);
        }

        return $schema;
    }

    private function inferItemSchema(array $items): array
    {
        if (empty($items)) {
            return ['type' => 'unknown'];
        }

        $types = [];
        foreach ($items as $item) {
            $types[] = $this->getSchemaType($item);
        }

        $uniqueTypes = array_unique($types);

        return [
            'type' => count($uniqueTypes) === 1 ? reset($uniqueTypes) : implode('|', $uniqueTypes),
            'count' => count($items),
        ];
    }

    private function isAssociativeArray(mixed $value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        if (empty($value)) {
            return true;
        }

        $keys = array_keys($value);
        $numericKeys = array_filter($keys, 'is_numeric');

        return count($numericKeys) === 0;
    }

    private function hasNumericKeys(mixed $value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        $keys = array_keys($value);
        return count(array_filter($keys, 'is_numeric')) > 0;
    }

    private function calculateSize(): int
    {
        return strlen(json_encode($this->data) ?: '');
    }

    private function calculateDepth(mixed $value = null, int $depth = 0): int
    {
        $value = $value ?? $this->data;

        if (!is_array($value) && !is_object($value)) {
            return $depth;
        }

        $maxDepth = $depth;
        foreach ((array)$value as $item) {
            $itemDepth = $this->calculateDepth($item, $depth + 1);
            $maxDepth = max($maxDepth, $itemDepth);
        }

        return $maxDepth;
    }

    private function countKeys(mixed $value, array $seen = []): int
    {
        if (!is_array($value) && !is_object($value)) {
            return 0;
        }

        $count = count((array)$value);

        foreach ((array)$value as $item) {
            if ((is_array($item) || is_object($item)) && !in_array(spl_object_hash($item) ?: '', $seen, true)) {
                $seen[] = spl_object_hash($item) ?? '';
                $count += $this->countKeys($item, $seen);
            }
        }

        return $count;
    }

    private function countNodes(mixed $value, int &$count = 0, array $seen = []): int
    {
        if (is_array($value) || is_object($value)) {
            $hash = spl_object_hash($value) ?: '';
            if (!in_array($hash, $seen, true)) {
                $seen[] = $hash;
                $count++;

                foreach ((array)$value as $item) {
                    $this->countNodes($item, $count, $seen);
                }
            }
        } else {
            $count++;
        }

        return $count;
    }

    private function countLeaves(mixed $value): int
    {
        if (!is_array($value) && !is_object($value)) {
            return 1;
        }

        $count = 0;
        foreach ((array)$value as $item) {
            if (!is_array($item) && !is_object($item)) {
                $count++;
            } else {
                $count += $this->countLeaves($item);
            }
        }

        return $count;
    }

    private function countArrays(mixed $value): int
    {
        if (!is_array($value) && !is_object($value)) {
            return 0;
        }

        $count = is_array($value) ? 1 : 0;

        foreach ((array)$value as $item) {
            $count += $this->countArrays($item);
        }

        return $count;
    }

    private function countObjects(mixed $value): int
    {
        if (!is_array($value) && !is_object($value)) {
            return 0;
        }

        $count = is_object($value) ? 1 : 0;

        foreach ((array)$value as $item) {
            $count += $this->countObjects($item);
        }

        return $count;
    }

    private function countNulls(mixed $value): int
    {
        if (!is_array($value) && !is_object($value)) {
            return is_null($value) ? 1 : 0;
        }

        $count = 0;

        foreach ((array)$value as $item) {
            $count += $this->countNulls($item);
        }

        return $count;
    }

    private function countScalars(mixed $value): int
    {
        if (!is_array($value) && !is_object($value)) {
            return is_scalar($value) ? 1 : 0;
        }

        $count = 0;

        foreach ((array)$value as $item) {
            $count += $this->countScalars($item);
        }

        return $count;
    }

    private function getParentPath(string $path): ?string
    {
        if ($path === '' || $path === '/') {
            return null;
        }

        $parts = explode('/', trim($path, '/'));
        if (count($parts) <= 1) {
            return null;
        }

        array_pop($parts);

        return '/' . implode('/', $parts);
    }

    private function getCallableSignature(callable $callable): string
    {
        if (is_string($callable)) {
            return $callable;
        }

        if (is_array($callable)) {
            $class = is_string($callable[0]) ? $callable[0] : $callable[0]::class;
            return sprintf('%s::%s', $class, $callable[1]);
        }

        if ($callable instanceof \Closure) {
            $reflection = new \ReflectionFunction($callable);
            return sprintf('Closure at %s:%d', $reflection->getFileName(), $reflection->getStartLine());
        }

        if (is_object($callable) && method_exists($callable, '__invoke')) {
            $class = $callable::class;
            $reflection = new \ReflectionMethod($callable, '__invoke');
            return sprintf('%s::__invoke (line %d)', $class, $reflection->getStartLine());
        }

        return 'Unknown callable';
    }

    private function formatValue(mixed $value): string
    {
        return match (true) {
            is_null($value) => 'null',
            is_bool($value) => $value ? 'true' : 'false',
            is_numeric($value) => (string)$value,
            default => (string)$value,
        };
    }

    private function formatSchema(array $schema, int $indent = 0): string
    {
        $output = '';
        $prefix = str_repeat(' ', $indent);

        foreach ($schema as $key => $value) {
            if (is_array($value)) {
                $output .= sprintf("%s%s:\n", $prefix, $key);
                $output .= $this->formatSchema($value, $indent + 2);
            } else {
                $output .= sprintf("%s%s: %s\n", $prefix, $key, $this->formatValue($value));
            }
        }

        return $output;
    }
}
