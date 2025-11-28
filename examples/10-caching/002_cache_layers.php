<?php

/**
 * Multi-Layer Caching
 *
 * Demonstrates layered caching strategies.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Cache\MemoryStore;

// Example 1: L1/L2 cache
print_section('1. Two-Layer Cache');

$l1Cache = new MemoryStore();  // In-memory
$l2Cache = new MemoryStore();  // Backing store

function getWithFallback($key, $l1, $l2, $fetcher) {
    // Check L1
    $value = $l1->get($key);
    if ($value !== null) {
        echo "  L1 hit\n";
        return $value;
    }
    
    // Check L2
    $value = $l2->get($key);
    if ($value !== null) {
        echo "  L2 hit, populating L1\n";
        $l1->set($key, $value);
        return $value;
    }
    
    // Fetch
    echo "  Cache miss, fetching\n";
    $value = $fetcher();
    $l1->set($key, $value);
    $l2->set($key, $value);
    return $value;
}

echo "Access 1:\n";
$value1 = getWithFallback('data:1', $l1Cache, $l2Cache, function() {
    return ['id' => 1, 'data' => 'value'];
});

echo "\nAccess 2 (same key):\n";
$value2 = getWithFallback('data:1', $l1Cache, $l2Cache, function() {
    return ['id' => 1, 'data' => 'value'];
});

print_section('Examples Complete');
