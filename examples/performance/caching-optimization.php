<?php

declare(strict_types=1);

/**
 * Performance - Query Caching and Optimization
 *
 * This example demonstrates performance optimization techniques including
 * query caching, lazy loading, and benchmarking.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Skpassegna\Json\Json;

echo "=== Performance - Caching and Optimization ===\n\n";

// Example 1: Query caching with cache store
echo "1. Query caching (avoid repeated evaluations):\n";
$data = json_encode([
    'users' => array_map(fn($i) => [
        'id' => $i,
        'name' => "User $i",
        'role' => $i % 3 === 0 ? 'admin' : 'user',
    ], range(1, 100)),
]);

$json = Json::parse($data);
$cache = Json::cache();

echo "   First query (computed and cached):\n";
$start = microtime(true);
$results1 = $json->queryWithCache('$.users[?(@.role=="admin")]', cache: $cache, ttl: 3600);
$time1 = microtime(true) - $start;
echo "     Found " . count($results1) . " admin users in " . ($time1 * 1000) . "ms\n";

echo "   Second query (from cache):\n";
$start = microtime(true);
$results2 = $json->queryWithCache('$.users[?(@.role=="admin")]', cache: $cache, ttl: 3600);
$time2 = microtime(true) - $start;
echo "     Found " . count($results2) . " admin users in " . ($time2 * 1000) . "ms\n";
echo "     Cache speedup: " . round($time1 / $time2, 1) . "x faster\n\n";

// Example 2: Lazy loading for deferred parsing
echo "2. Lazy loading (parse only when needed):\n";

// Create a lazy loader that simulates reading from a file
$lazyJson = Json::lazy(function () {
    echo "     [Lazy] Actually parsing JSON now...\n";
    return json_decode(json_encode([
        'config' => [
            'database' => [
                'host' => 'localhost',
                'port' => 5432,
                'username' => 'admin',
            ],
        ],
    ]), true);
}, prefetch: false);

echo "   Created lazy-loaded JSON (not parsed yet)\n";
echo "   Accessing nested value:\n";
echo "     Database host: " . ($lazyJson['config']['database']['host'] ?? 'not found') . "\n\n";

// Example 3: Streaming for large files
echo "3. Streaming for memory efficiency (not fully loaded into memory):\n";
echo "   With streaming, you can process large NDJSON files line-by-line\n";
echo "   Example: " . 'php examples/streaming/parse_large.php' . "\n\n";

// Example 4: Flattening for quick lookups
echo "4. Flattening for quick key lookups:\n";
$complexData = [
    'user' => [
        'profile' => [
            'name' => 'Alice',
            'email' => 'alice@example.com',
        ],
        'settings' => [
            'theme' => 'dark',
            'notifications' => true,
        ],
    ],
];

$json = Json::create($complexData);
$flat = $json->flatten('.');

echo "   Original structure requires nested access\n";
echo "   Flattened version:\n";
foreach ($flat as $key => $value) {
    echo "     $key => $value\n";
}
echo "\n";

// Example 5: Benchmark streaming vs. parsing
echo "5. Performance comparison:\n";
echo "   Parsing 10,000 items at once:\n";
$largeArray = array_fill(0, 10000, ['id' => 1, 'value' => 'test']);

$start = microtime(true);
$json = Json::create($largeArray);
$parseTime = microtime(true) - $start;

$start = microtime(true);
$string = json_encode($largeArray);
$encodeTime = microtime(true) - $start;

$start = microtime(true);
$decoded = json_decode($string, true);
$decodeTime = microtime(true) - $start;

echo "     Parse time: " . round($parseTime * 1000, 2) . "ms\n";
echo "     Encode time: " . round($encodeTime * 1000, 2) . "ms\n";
echo "     Decode time: " . round($decodeTime * 1000, 2) . "ms\n\n";

echo "Performance optimization examples completed!\n";
echo "For full benchmarking, run: composer benchmark\n";
