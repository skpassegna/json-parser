<?php

declare(strict_types=1);

namespace Skpassegna\Json\Json;

use Skpassegna\Json\Contracts\PathInterface;
use Skpassegna\Json\Exceptions\RuntimeException;

/**
 * JSONPath implementation for querying JSON documents
 * Supports basic JSONPath syntax:
 * $ - root object
 * . - child operator
 * [] - subscript operator
 * * - wildcard
 * [start:end:step] - array slice
 * [?(@.property op value)] - filter expression
 */
class Path implements PathInterface
{
    private Serializer $serializer;
    private array $cache = [];

    public function __construct()
    {
        $this->serializer = new Serializer();
    }

    /**
     * Query a JSON document using JSONPath expression
     *
     * @param string|array<mixed> $document JSON document
     * @param string $path JSONPath expression
     * @return array<mixed> Matching values
     * @throws RuntimeException If the query fails
     */
    public function query(string|array $document, string $path): array
    {
        try {
            $data = is_string($document) ? $this->serializer->deserialize($document, true) : $document;
            return $this->evaluate($data, $this->parsePath($path));
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to evaluate JSONPath: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Parse a JSONPath expression into tokens
     *
     * @param string $path JSONPath expression
     * @return array<array{type:string,value:string}> Parsed tokens
     */
    private function parsePath(string $path): array
    {
        if (isset($this->cache[$path])) {
            return $this->cache[$path];
        }

        $tokens = [];
        $length = strlen($path);
        $i = 0;

        while ($i < $length) {
            $char = $path[$i];

            switch ($char) {
                case '$':
                    $tokens[] = ['type' => 'root', 'value' => '$'];
                    $i++;
                    break;

                case '.':
                    if (isset($path[$i + 1]) && $path[$i + 1] === '.') {
                        $tokens[] = ['type' => 'recursive', 'value' => '..'];
                        $i += 2;
                    } else {
                        $tokens[] = ['type' => 'dot', 'value' => '.'];
                        $i++;
                    }
                    break;

                case '[':
                    $end = $this->findClosingBracket($path, $i);
                    $content = substr($path, $i + 1, $end - $i - 1);

                    if (str_starts_with($content, '?')) {
                        $tokens[] = ['type' => 'filter', 'value' => trim($content, '?@)')];
                    } elseif (str_contains($content, ':')) {
                        $tokens[] = ['type' => 'slice', 'value' => $content];
                    } else {
                        $tokens[] = ['type' => 'subscript', 'value' => trim($content, '"\'')];
                    }

                    $i = $end + 1;
                    break;

                case '*':
                    $tokens[] = ['type' => 'wildcard', 'value' => '*'];
                    $i++;
                    break;

                default:
                    if (ctype_alnum($char) || $char === '_') {
                        $identifier = '';
                        while ($i < $length && (ctype_alnum($path[$i]) || $path[$i] === '_')) {
                            $identifier .= $path[$i];
                            $i++;
                        }
                        $tokens[] = ['type' => 'identifier', 'value' => $identifier];
                    } else {
                        $i++;
                    }
            }
        }

        $this->cache[$path] = $tokens;
        return $tokens;
    }

    /**
     * Find the closing bracket position
     *
     * @param string $path JSONPath expression
     * @param int $start Starting position
     * @return int Position of closing bracket
     * @throws RuntimeException If no closing bracket is found
     */
    private function findClosingBracket(string $path, int $start): int
    {
        $length = strlen($path);
        $depth = 1;

        for ($i = $start + 1; $i < $length; $i++) {
            if ($path[$i] === '[') {
                $depth++;
            } elseif ($path[$i] === ']') {
                $depth--;
                if ($depth === 0) {
                    return $i;
                }
            }
        }

        throw new RuntimeException('Unclosed bracket in JSONPath expression');
    }

    /**
     * Evaluate JSONPath tokens against data
     *
     * @param mixed $data Target data
     * @param array<array{type:string,value:string}> $tokens Path tokens
     * @return array<mixed> Matching values
     */
    private function evaluate(mixed $data, array $tokens): array
    {
        $current = [$data];

        foreach ($tokens as $token) {
            $next = [];

            foreach ($current as $item) {
                match ($token['type']) {
                    'root' => $next[] = $data,
                    'dot', 'identifier' => $this->handleDotNotation($item, $token['value'], $next),
                    'recursive' => $this->handleRecursive($item, $tokens, $next),
                    'wildcard' => $this->handleWildcard($item, $next),
                    'subscript' => $this->handleSubscript($item, $token['value'], $next),
                    'slice' => $this->handleSlice($item, $token['value'], $next),
                    'filter' => $this->handleFilter($item, $token['value'], $next),
                    default => throw new RuntimeException("Unknown token type: {$token['type']}")
                };
            }

            $current = $next;
        }

        return $current;
    }

    /**
     * Handle dot notation access
     *
     * @param mixed $data Current data item
     * @param string $key Property key
     * @param array<mixed> $results Results array
     * @return void
     */
    private function handleDotNotation(mixed $data, string $key, array &$results): void
    {
        if (is_array($data) && array_key_exists($key, $data)) {
            $results[] = $data[$key];
        } elseif (is_object($data) && property_exists($data, $key)) {
            $results[] = $data->$key;
        }
    }

    /**
     * Handle recursive descent
     *
     * @param mixed $data Current data item
     * @param array<array{type:string,value:string}> $tokens Path tokens
     * @param array<mixed> $results Results array
     * @return void
     */
    private function handleRecursive(mixed $data, array $tokens, array &$results): void
    {
        if (is_array($data) || is_object($data)) {
            foreach ((array)$data as $value) {
                $results = array_merge($results, $this->evaluate($value, $tokens));
            }
        }
    }

    /**
     * Handle wildcard operator
     *
     * @param mixed $data Current data item
     * @param array<mixed> $results Results array
     * @return void
     */
    private function handleWildcard(mixed $data, array &$results): void
    {
        if (is_array($data)) {
            foreach ($data as $value) {
                $results[] = $value;
            }
        }
    }

    /**
     * Handle array subscript
     *
     * @param mixed $data Current data item
     * @param string $index Array index
     * @param array<mixed> $results Results array
     * @return void
     */
    private function handleSubscript(mixed $data, string $index, array &$results): void
    {
        if (is_array($data) && array_key_exists($index, $data)) {
            $results[] = $data[$index];
        }
    }

    /**
     * Handle array slice
     *
     * @param mixed $data Current data item
     * @param string $slice Slice expression
     * @param array<mixed> $results Results array
     * @return void
     */
    private function handleSlice(mixed $data, string $slice, array &$results): void
    {
        if (!is_array($data)) {
            return;
        }

        $parts = explode(':', $slice);
        $start = $parts[0] === '' ? 0 : (int)$parts[0];
        $end = $parts[1] === '' ? null : (int)$parts[1];
        $step = isset($parts[2]) ? (int)$parts[2] : 1;

        $length = count($data);
        $start = $start < 0 ? $length + $start : $start;
        $end = $end === null ? $length : ($end < 0 ? $length + $end : $end);

        for ($i = $start; $i < $end; $i += $step) {
            if (isset($data[$i])) {
                $results[] = $data[$i];
            }
        }
    }

    /**
     * Handle filter expression
     *
     * @param mixed $data Current data item
     * @param string $expression Filter expression
     * @param array<mixed> $results Results array
     * @return void
     */
    private function handleFilter(mixed $data, string $expression, array &$results): void
    {
        if (!is_array($data)) {
            return;
        }

        foreach ($data as $item) {
            if ($this->evaluateFilterExpression($item, $expression)) {
                $results[] = $item;
            }
        }
    }

    /**
     * Evaluate a filter expression against an item
     *
     * @param mixed $item Data item
     * @param string $expression Filter expression
     * @return bool Whether the item matches the filter
     */
    private function evaluateFilterExpression(mixed $item, string $expression): bool
    {
        // Simple property existence check
        if (str_starts_with($expression, '@.')) {
            $property = substr($expression, 2);
            return is_array($item) && array_key_exists($property, $item);
        }

        // Basic comparison operations
        if (preg_match('/^@\.(\w+)\s*(==|!=|>|<|>=|<=)\s*(.+)$/', $expression, $matches)) {
            [$_, $property, $operator, $value] = $matches;
            
            if (!is_array($item) || !array_key_exists($property, $item)) {
                return false;
            }

            $itemValue = $item[$property];
            $compareValue = trim($value, '"\'');

            return match ($operator) {
                '==' => $itemValue == $compareValue,
                '!=' => $itemValue != $compareValue,
                '>' => $itemValue > $compareValue,
                '<' => $itemValue < $compareValue,
                '>=' => $itemValue >= $compareValue,
                '<=' => $itemValue <= $compareValue,
                default => false
            };
        }

        return false;
    }
}
