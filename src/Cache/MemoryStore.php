<?php

declare(strict_types=1);

namespace Skpassegna\Json\Cache;

use Skpassegna\Json\Contracts\CacheStoreInterface;

final class MemoryStore implements CacheStoreInterface
{
    /**
     * @var array<string, array{value: mixed, expires: ?int}>
     */
    private array $store = [];

    /**
     * @inheritDoc
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (!isset($this->store[$key])) {
            return $default;
        }

        $entry = $this->store[$key];
        
        if ($entry['expires'] !== null && time() >= $entry['expires']) {
            unset($this->store[$key]);
            return $default;
        }

        return $entry['value'];
    }

    /**
     * @inheritDoc
     */
    public function put(string $key, mixed $value, ?int $ttl = null): bool
    {
        $expires = null;
        if ($ttl !== null && $ttl > 0) {
            $expires = time() + $ttl;
        }

        $this->store[$key] = [
            'value' => $value,
            'expires' => $expires,
        ];

        return true;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        if (!isset($this->store[$key])) {
            return false;
        }

        $entry = $this->store[$key];
        
        if ($entry['expires'] !== null && time() >= $entry['expires']) {
            unset($this->store[$key]);
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function forget(string $key): bool
    {
        if (isset($this->store[$key])) {
            unset($this->store[$key]);
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function flush(): bool
    {
        $this->store = [];
        return true;
    }
}
