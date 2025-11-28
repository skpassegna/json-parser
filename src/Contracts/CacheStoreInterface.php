<?php

declare(strict_types=1);

namespace Skpassegna\Json\Contracts;

interface CacheStoreInterface
{
    /**
     * Get a value from the cache.
     *
     * @param string $key The cache key
     * @param mixed $default Default value if key not found
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Set a value in the cache.
     *
     * @param string $key The cache key
     * @param mixed $value The value to cache
     * @param ?int $ttl Time to live in seconds (null = forever)
     * @return bool True if successful
     */
    public function put(string $key, mixed $value, ?int $ttl = null): bool;

    /**
     * Check if a key exists in cache.
     *
     * @param string $key The cache key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Delete a value from the cache.
     *
     * @param string $key The cache key
     * @return bool True if successful
     */
    public function forget(string $key): bool;

    /**
     * Clear all cache entries.
     *
     * @return bool True if successful
     */
    public function flush(): bool;
}
