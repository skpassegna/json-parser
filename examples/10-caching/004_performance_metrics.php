<?php

/**
 * Cache Performance Metrics
 *
 * Measure and track cache performance.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Cache\MemoryStore;

print_section('Cache Performance');

$cache = new MemoryStore();
$stats = ['hits' => 0, 'misses' => 0];

// Simulate cache usage
for ($i = 0; $i < 10; $i++) {
    $key = 'item:' . ($i % 3);
    
    if ($cache->get($key) !== null) {
        $stats['hits']++;
    } else {
        $stats['misses']++;
        $cache->set($key, ['data' => random_int(1, 100)]);
    }
}

$total = $stats['hits'] + $stats['misses'];
$hitRate = ($stats['hits'] / $total) * 100;

echo "Total accesses: $total\n";
echo "Hits: " . $stats['hits'] . "\n";
echo "Misses: " . $stats['misses'] . "\n";
echo "Hit rate: " . number_format($hitRate, 1) . "%\n";

print_section('Examples Complete');
