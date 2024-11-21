<?php

declare(strict_types=1);

namespace Skpassegna\Json\Json;

use Skpassegna\Json\Contracts\PointerInterface;
use Skpassegna\Json\Exceptions\RuntimeException;

class Pointer implements PointerInterface
{
    private Serializer $serializer;

    public function __construct()
    {
        $this->serializer = new Serializer();
    }

    /**
     * @inheritDoc
     */
    public function get(string|array $document, string $pointer): mixed
    {
        try {
            $data = is_string($document) ? $this->serializer->deserialize($document, true) : $document;
            
            if ($pointer === '') {
                return $data;
            }

            $segments = $this->parsePointer($pointer);
            $current = &$data;

            foreach ($segments as $segment) {
                if (!$this->hasSegment($current, $segment)) {
                    throw new RuntimeException("Path not found: $pointer");
                }
                $current = &$current[$segment];
            }

            return $current;
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to get value: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function set(string|array $document, string $pointer, mixed $value, bool $mutate = false): string|array
    {
        try {
            $data = is_string($document) ? $this->serializer->deserialize($document, true) : $document;
            $result = $mutate ? $data : json_decode(json_encode($data), true);

            if ($pointer === '') {
                return is_string($document) ? $this->serializer->serialize($value) : $value;
            }

            $segments = $this->parsePointer($pointer);
            $current = &$result;

            for ($i = 0; $i < count($segments) - 1; $i++) {
                $segment = $segments[$i];
                if (!$this->hasSegment($current, $segment)) {
                    if (is_numeric($segments[$i + 1])) {
                        $current[$segment] = [];
                    } else {
                        $current[$segment] = [];
                    }
                }
                $current = &$current[$segment];
            }

            $lastSegment = end($segments);
            if ($lastSegment === '-' && is_array($current)) {
                $current[] = $value;
            } else {
                $current[$lastSegment] = $value;
            }

            return is_string($document) ? $this->serializer->serialize($result) : $result;
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to set value: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function remove(string|array $document, string $pointer, bool $mutate = false): string|array
    {
        try {
            if ($pointer === '') {
                throw new RuntimeException('Cannot remove document root');
            }

            $data = is_string($document) ? $this->serializer->deserialize($document, true) : $document;
            $result = $mutate ? $data : json_decode(json_encode($data), true);

            $segments = $this->parsePointer($pointer);
            $current = &$result;

            for ($i = 0; $i < count($segments) - 1; $i++) {
                $segment = $segments[$i];
                if (!$this->hasSegment($current, $segment)) {
                    throw new RuntimeException("Path not found: $pointer");
                }
                $current = &$current[$segment];
            }

            $lastSegment = end($segments);
            if (!$this->hasSegment($current, $lastSegment)) {
                throw new RuntimeException("Path not found: $pointer");
            }

            if (is_array($current)) {
                unset($current[$lastSegment]);
                if (is_numeric($lastSegment)) {
                    $current = array_values($current);
                }
            }

            return is_string($document) ? $this->serializer->serialize($result) : $result;
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to remove value: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function has(string|array $document, string $pointer): bool
    {
        try {
            $this->get($document, $pointer);
            return true;
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function create(array $segments): string
    {
        $encodedSegments = array_map(function ($segment) {
            return str_replace('~', '~0', str_replace('/', '~1', (string)$segment));
        }, $segments);

        return '/' . implode('/', $encodedSegments);
    }

    /**
     * Parse a JSON Pointer into path segments
     *
     * @param string $pointer The JSON Pointer string
     * @return string[] The path segments
     * @throws RuntimeException If pointer format is invalid
     */
    private function parsePointer(string $pointer): array
    {
        if ($pointer === '') {
            return [];
        }

        if (!str_starts_with($pointer, '/')) {
            throw new RuntimeException('Invalid pointer format: must start with "/"');
        }

        $segments = explode('/', substr($pointer, 1));
        return array_map(function ($segment) {
            return str_replace('~1', '/', str_replace('~0', '~', $segment));
        }, $segments);
    }

    /**
     * Check if a segment exists in the current data
     *
     * @param array<mixed>|mixed $data Current data
     * @param string $segment Path segment to check
     * @return bool True if segment exists
     */
    private function hasSegment(mixed $data, string $segment): bool
    {
        if (!is_array($data)) {
            return false;
        }

        if ($segment === '-' && is_array($data)) {
            return true;
        }

        return array_key_exists($segment, $data);
    }
}
