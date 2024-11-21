<?php

declare(strict_types=1);

namespace Skpassegna\Json\Utils;

class JsonPath
{
    private mixed $data;

    public function __construct(mixed $data)
    {
        $this->data = $data;
    }

    /**
     * Query JSON data using JSONPath expression.
     *
     * @param string $path
     * @return array
     */
    public function query(string $path): array
    {
        $segments = $this->parsePath($path);
        return $this->evaluateSegments($segments, $this->data);
    }

    /**
     * Parse JSONPath expression into segments.
     *
     * @param string $path
     * @return array
     */
    private function parsePath(string $path): array
    {
        $path = ltrim($path, '$');
        $segments = [];
        $current = '';
        $inBracket = false;
        $length = strlen($path);

        for ($i = 0; $i < $length; $i++) {
            $char = $path[$i];

            if ($char === '[') {
                if ($current !== '') {
                    $segments[] = $current;
                    $current = '';
                }
                $inBracket = true;
            } elseif ($char === ']') {
                if ($current !== '') {
                    $segments[] = $this->parseArrayAccess($current);
                    $current = '';
                }
                $inBracket = false;
            } elseif ($char === '.' && !$inBracket) {
                if ($current !== '') {
                    $segments[] = $current;
                    $current = '';
                }
            } else {
                $current .= $char;
            }
        }

        if ($current !== '') {
            $segments[] = $current;
        }

        return $segments;
    }

    /**
     * Parse array access expression.
     *
     * @param string $expression
     * @return array|string
     */
    private function parseArrayAccess(string $expression): array|string
    {
        if ($expression === '*') {
            return '*';
        }

        if (str_contains($expression, ':')) {
            $parts = explode(':', $expression);
            return [
                'slice' => [
                    'start' => $parts[0] === '' ? null : (int)$parts[0],
                    'end' => $parts[1] === '' ? null : (int)$parts[1],
                    'step' => isset($parts[2]) ? (int)$parts[2] : 1,
                ],
            ];
        }

        if (str_starts_with($expression, '?')) {
            return ['filter' => substr($expression, 1)];
        }

        return $expression;
    }

    /**
     * Evaluate JSONPath segments.
     *
     * @param array $segments
     * @param mixed $data
     * @return array
     */
    private function evaluateSegments(array $segments, mixed $data): array
    {
        $results = [$data];

        foreach ($segments as $segment) {
            $temp = [];

            foreach ($results as $result) {
                if (!is_array($result) && !is_object($result)) {
                    continue;
                }

                if ($segment === '*') {
                    $temp = array_merge($temp, $this->handleWildcard($result));
                } elseif (is_array($segment) && isset($segment['slice'])) {
                    $temp = array_merge($temp, $this->handleSlice($result, $segment['slice']));
                } elseif (is_array($segment) && isset($segment['filter'])) {
                    $temp = array_merge($temp, $this->handleFilter($result, $segment['filter']));
                } else {
                    $value = is_array($result) ? ($result[$segment] ?? null) : ($result->{$segment} ?? null);
                    if ($value !== null) {
                        $temp[] = $value;
                    }
                }
            }

            $results = $temp;
        }

        return $results;
    }

    /**
     * Handle wildcard operator.
     *
     * @param array|object $data
     * @return array
     */
    private function handleWildcard(array|object $data): array
    {
        return is_array($data) ? array_values($data) : array_values((array)$data);
    }

    /**
     * Handle array slice.
     *
     * @param array|object $data
     * @param array $slice
     * @return array
     */
    private function handleSlice(array|object $data, array $slice): array
    {
        $array = is_array($data) ? $data : (array)$data;
        $start = $slice['start'] ?? 0;
        $end = $slice['end'] ?? count($array);
        $step = $slice['step'] ?? 1;

        $result = [];
        for ($i = $start; $i < $end; $i += $step) {
            if (isset($array[$i])) {
                $result[] = $array[$i];
            }
        }

        return $result;
    }

    /**
     * Handle filter expression.
     *
     * @param array|object $data
     * @param string $filter
     * @return array
     */
    private function handleFilter(array|object $data, string $filter): array
    {
        $result = [];
        $array = is_array($data) ? $data : (array)$data;

        foreach ($array as $key => $value) {
            if ($this->evaluateFilter($filter, $value, $key)) {
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * Evaluate filter expression.
     *
     * @param string $filter
     * @param mixed $value
     * @param string|int $key
     * @return bool
     */
    private function evaluateFilter(string $filter, mixed $value, string|int $key): bool
    {
        // Simple equality check
        if (preg_match('/^@\s*==\s*(.+)$/', $filter, $matches)) {
            $compareValue = trim($matches[1]);
            if (is_numeric($compareValue)) {
                return $value == $compareValue;
            }
            return $value == trim($compareValue, '"\'');
        }

        // Greater than
        if (preg_match('/^@\s*>\s*(.+)$/', $filter, $matches)) {
            return is_numeric($value) && $value > (float)$matches[1];
        }

        // Less than
        if (preg_match('/^@\s*<\s*(.+)$/', $filter, $matches)) {
            return is_numeric($value) && $value < (float)$matches[1];
        }

        // Contains (for strings)
        if (preg_match('/^@\s*=~\s*(.+)$/', $filter, $matches)) {
            $searchValue = trim($matches[1], '"\'');
            return is_string($value) && str_contains($value, $searchValue);
        }

        return false;
    }
}
