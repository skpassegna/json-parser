<?php

declare(strict_types=1);

/**
 * Lazy Loading Example
 * 
 * Demonstrates deferred JSON parsing until data is actually accessed,
 * reducing memory usage for configurations and large datasets.
 */

use Skpassegna\Json\Json;

require __DIR__ . '/../../vendor/autoload.php';

// Example 1: Defer parsing until access
echo "=== Example 1: Lazy Loading Deferred ===\n";

$loader = function () {
    echo "  [Loading JSON data now...]\n";
    return [
        'database' => [
            'host' => 'localhost',
            'port' => 5432,
            'name' => 'mydb',
        ],
        'api' => [
            'url' => 'https://api.example.com',
            'timeout' => 30,
        ],
    ];
};

$lazyConfig = Json::lazy($loader, prefetch: false);
echo "Proxy created (not loaded yet)\n";
echo "Is loaded? " . ($lazyConfig->isLoaded() ? 'Yes' : 'No') . "\n";

echo "\nAccessing database host...\n";
$host = $lazyConfig['database']['host'];
echo "Host: $host\n";
echo "Is loaded now? " . ($lazyConfig->isLoaded() ? 'Yes' : 'No') . "\n";

// Example 2: Prefetch on creation
echo "\n=== Example 2: Prefetch Mode ===\n";

$lazyConfigPrefetch = Json::lazy($loader, prefetch: true);
echo "Proxy created with prefetch\n";
echo "Is loaded? " . ($lazyConfigPrefetch->isLoaded() ? 'Yes' : 'No') . "\n";

// Example 3: Working with arrays via ArrayAccess
echo "\n=== Example 3: Array Access ===\n";

$dataLoader = fn () => ['users' => ['Alice', 'Bob', 'Charlie'], 'count' => 3];
$lazy = Json::lazy($dataLoader);

echo "User count: " . count($lazy['users']) . "\n";
echo "First user: " . $lazy['users'][0] . "\n";

// Example 4: Iteration over lazy data
echo "\n=== Example 4: Iteration ===\n";

$itemsLoader = fn () => ['item1' => 'value1', 'item2' => 'value2', 'item3' => 'value3'];
$lazyItems = Json::lazy($itemsLoader);

foreach ($lazyItems as $key => $value) {
    echo "$key => $value\n";
}

// Example 5: Magic property access
echo "\n=== Example 5: Magic Property Access ===\n";

$objectLoader = fn () => (object) ['name' => 'John', 'age' => 30, 'city' => 'NYC'];
$lazPerson = Json::lazy($objectLoader);

echo "Name: " . $lazPerson->name . "\n";
echo "Age: " . $lazPerson->age . "\n";
echo "City: " . $lazPerson->city . "\n";

// Example 6: Reset and reload
echo "\n=== Example 6: Reset and Reload ===\n";

$callCount = 0;
$counterLoader = function () use (&$callCount) {
    $callCount++;
    echo "  [Load called $callCount times]\n";
    return ['count' => $callCount];
};

$lazyCounter = Json::lazy($counterLoader);
$lazyCounter->getData();
$lazyCounter->getData();
$lazyCounter->reset();
$lazyCounter->getData();

echo "\n✓ Lazy loading examples completed!\n";
