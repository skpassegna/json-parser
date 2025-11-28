<?php

/**
 * Lazy Loading Performance Patterns
 *
 * Demonstrates performance optimization using lazy loading.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Streaming\LazyJsonProxy;

// Example 1: Measure lazy vs eager
print_section('1. Performance Comparison');

$largeJson = json_encode(array_fill(0, 5000, [
    'id' => random_int(1, 1000000),
    'data' => 'x' * 100,
]));

// Eager
$startEager = memory_get_usage();
$eager = json_decode($largeJson, true);
$memEager = memory_get_usage() - $startEager;

// Lazy
$startLazy = memory_get_usage();
$lazy = new LazyJsonProxy($largeJson);
$memLazy = memory_get_usage() - $startLazy;

echo "Eager loading: " . ($memEager / 1024) . " KB\n";
echo "Lazy loading: " . ($memLazy / 1024) . " KB\n";
echo "Savings: " . (($memEager - $memLazy) / 1024) . " KB\n";

print_section('Examples Complete');
