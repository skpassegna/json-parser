<?php

/**
 * Cache Invalidation Patterns
 *
 * Different strategies for invalidating cached data.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Cache\MemoryStore;

// Example: Invalidation patterns
print_section('Cache Invalidation');

$cache = new MemoryStore();

// Exact key invalidation
$cache->set('user:1', ['name' => 'Alice']);
$cache->delete('user:1');
echo "Exact key deleted\n";

// Wildcard pattern simulation
$keys = ['item:1', 'item:2', 'item:3'];
foreach ($keys as $key) {
    $cache->set($key, ['id' => random_int(1, 10)]);
}

// Delete matching pattern
foreach ($keys as $key) {
    $cache->delete($key);
}
echo "Pattern-based deletion\n";

print_section('Examples Complete');
