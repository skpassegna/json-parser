<?php

declare(strict_types=1);

/**
 * Caching Queries Example
 * 
 * Demonstrates caching expensive JSONPath queries and validations
 * to improve performance for repeated access patterns.
 */

use Skpassegna\Json\Cache\MemoryStore;
use Skpassegna\Json\Json;

require __DIR__ . '/../../vendor/autoload.php';

// Example 1: Basic query caching
echo "=== Example 1: Query Caching ===\n";

$json = Json::parse([
    'users' => [
        ['id' => 1, 'name' => 'Alice', 'role' => 'admin'],
        ['id' => 2, 'name' => 'Bob', 'role' => 'user'],
        ['id' => 3, 'name' => 'Charlie', 'role' => 'user'],
    ],
    'settings' => [
        'theme' => 'dark',
        'notifications' => true,
    ],
]);

$cache = Json::cache();

echo "First query (will be cached)...\n";
$admins = $json->queryWithCache('$.users[?(@.role=="admin")]', $cache);
echo "Found " . count($admins) . " admin(s)\n";

echo "\nSecond query (from cache)...\n";
$admins2 = $json->queryWithCache('$.users[?(@.role=="admin")]', $cache);
echo "Found " . count($admins2) . " admin(s)\n";

// Example 2: Custom cache with TTL
echo "\n=== Example 2: Cache with TTL ===\n";

$cache = new MemoryStore();

echo "Storing data with 1 second TTL...\n";
$cache->put('query_result', ['user1', 'user2'], ttl: 1);
echo "Data exists: " . ($cache->has('query_result') ? 'Yes' : 'No') . "\n";

echo "Waiting 2 seconds...\n";
sleep(2);

echo "Data exists now: " . ($cache->has('query_result') ? 'Yes' : 'No') . "\n";

// Example 3: Cache management
echo "\n=== Example 3: Cache Management ===\n";

$cache = new MemoryStore();

$cache->put('key1', 'value1');
$cache->put('key2', 'value2');
$cache->put('key3', 'value3');

echo "Cache has key1: " . ($cache->has('key1') ? 'Yes' : 'No') . "\n";
echo "Cache value: " . $cache->get('key1') . "\n";

$cache->forget('key2');
echo "After forgetting key2: " . ($cache->has('key2') ? 'Yes' : 'No') . "\n";

$cache->flush();
echo "After flush: " . ($cache->has('key1') ? 'Yes' : 'No') . "\n";

// Example 4: Builder with caching
echo "\n=== Example 4: Streaming Builder with Cache ===\n";

$builder = Json::streaming()
    ->withCache(new MemoryStore(), ttl: 3600)
    ->withChunkSize(4096);

echo "Cache enabled: " . ($builder->getCache() !== null ? 'Yes' : 'No') . "\n";
echo "Cache TTL: " . $builder->getCacheTtl() . " seconds\n";
echo "Chunk size: " . $builder->getChunkSize() . " bytes\n";

// Example 5: Implementing cache invalidation pattern
echo "\n=== Example 5: Cache Invalidation ===\n";

class CachedJsonManager
{
    private Json $json;
    private MemoryStore $cache;
    private string $cachePrefix = 'json:';

    public function __construct(array $data)
    {
        $this->json = Json::parse($data);
        $this->cache = new MemoryStore();
    }

    public function query(string $path): array
    {
        $cacheKey = $this->cachePrefix . md5($path);
        
        if ($this->cache->has($cacheKey)) {
            echo "  [Cache hit]\n";
            return $this->cache->get($cacheKey, []);
        }

        echo "  [Cache miss, querying...]\n";
        $results = $this->json->query($path);
        $this->cache->put($cacheKey, $results, ttl: 600);
        
        return $results;
    }

    public function invalidateCache(string $pattern = '*'): void
    {
        if ($pattern === '*') {
            $this->cache->flush();
            echo "  [Cache cleared]\n";
        } else {
            $this->cache->flush(); // Simplified for example
            echo "  [Pattern cache invalidated]\n";
        }
    }
}

$manager = new CachedJsonManager([
    'products' => [
        ['id' => 1, 'name' => 'Product A', 'price' => 10],
        ['id' => 2, 'name' => 'Product B', 'price' => 20],
    ],
]);

echo "Query 1: ";
$manager->query('$.products[*]');

echo "Query 2 (same): ";
$manager->query('$.products[*]');

echo "Invalidating cache...\n";
$manager->invalidateCache();

echo "Query 3 (after invalidation): ";
$manager->query('$.products[*]');

echo "\n✓ Caching examples completed!\n";
