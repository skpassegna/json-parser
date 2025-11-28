<?php

/**
 * Lazy Loading with Proxies
 *
 * Demonstrates deferred parsing and lazy evaluation of JSON data,
 * loading values only when accessed.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;
use Skpassegna\Json\Streaming\LazyJsonProxy;

// Example 1: Create lazy proxy from string
print_section('1. Lazy Parsing with Proxy');
$jsonString = '{"user": {"name": "John", "email": "john@example.com", "profile": {"bio": "Developer", "location": "NYC"}}}';

// Create lazy proxy - doesn't parse yet
$lazy = new LazyJsonProxy($jsonString);

echo "Lazy proxy created (not parsed yet)\n";
echo "Accessing data (triggers parsing):\n";

// Now it parses when we access
$name = $lazy->get('user.name');
echo "Name: $name\n";

// Example 2: Compare eager vs lazy loading
print_section('2. Eager vs Lazy Loading');

// Eager - parses immediately
$startEager = microtime(true);
$eager = Json::parse($jsonString);
$eagerTime = microtime(true) - $startEager;
echo "Eager parsing time: " . ($eagerTime * 1000000) . " microseconds\n";

// Lazy - defers parsing
$startLazy = microtime(true);
$lazy = new LazyJsonProxy($jsonString);
$lazyTime = microtime(true) - $startLazy;
echo "Lazy parsing time: " . ($lazyTime * 1000000) . " microseconds\n";

// Access triggers parsing
$startAccess = microtime(true);
$lazyName = $lazy->get('user.name');
$accessTime = microtime(true) - $startAccess;
echo "Lazy access time: " . ($accessTime * 1000000) . " microseconds\n";

// Example 3: Selective data access
print_section('3. Selective Data Access');
$complexJson = json_encode([
    'user' => [
        'id' => 1,
        'name' => 'Alice',
        'email' => 'alice@example.com',
        'profile' => [
            'bio' => 'Software engineer',
            'avatar' => 'https://example.com/avatar.jpg',
            'social' => [
                'twitter' => 'alice_dev',
                'github' => 'alice-dev',
                'linkedin' => 'alice-developer',
            ],
        ],
    ],
    'large_data' => json_encode(array_fill(0, 1000, 'value')),
]);

$lazy = new LazyJsonProxy($complexJson);

// Access only what we need
$userId = $lazy->get('user.id');
$userName = $lazy->get('user.name');

echo "User: $userId - $userName\n";
echo "Large data not accessed yet\n";

// Example 4: Partial data extraction
print_section('4. Partial Data Extraction');
$configJson = file_get_contents(get_example_file('config.json'));
$lazy = new LazyJsonProxy($configJson);

echo "Config loaded lazily\n";
echo "Database host: " . $lazy->get('database.connections.mysql.host') . "\n";
echo "Cache driver: " . $lazy->get('cache.default') . "\n";

// Example 5: With file streaming
print_section('5. Lazy Loading from File');
$usersFile = get_example_file('users.json');

if (file_exists($usersFile)) {
    $fileContent = file_get_contents($usersFile);
    $lazy = new LazyJsonProxy($fileContent);
    
    echo "File loaded as lazy proxy\n";
    echo "Total users: " . count($lazy->get('users')) . "\n";
    echo "First user: " . $lazy->get('users.0.name') . "\n";
}

// Example 6: Lazy vs memory usage
print_section('6. Memory Efficiency Comparison');
// Large dataset simulation
$largeData = [];
for ($i = 0; $i < 1000; $i++) {
    $largeData[] = [
        'id' => $i,
        'data' => 'Some data ' . str_repeat('x', 100),
        'nested' => [
            'value' => $i * 2,
            'details' => 'Extra details for item ' . $i,
        ],
    ];
}

$largeJson = json_encode(['items' => $largeData]);

echo "JSON string size: " . strlen($largeJson) . " bytes\n";

// Eager load
$startMem = memory_get_usage();
$eager = Json::parse($largeJson);
$eagerMem = memory_get_usage() - $startMem;
echo "Eager memory usage: " . ($eagerMem / 1024) . " KB\n";

// Lazy load
$startMemLazy = memory_get_usage();
$lazy = new LazyJsonProxy($largeJson);
$lazyMem = memory_get_usage() - $startMemLazy;
echo "Lazy memory usage: " . ($lazyMem / 1024) . " KB\n";

// Example 7: Multiple accesses
print_section('7. Multiple Lazy Accesses');
$json = '{"status": "active", "count": 42, "data": {"nested": "value"}}';
$lazy = new LazyJsonProxy($json);

// Multiple accesses after parsing
echo "First access: " . $lazy->get('status') . "\n";
echo "Second access: " . $lazy->get('count') . "\n";
echo "Third access: " . $lazy->get('data.nested') . "\n";
echo "All from same parsed instance\n";

// Example 8: Conditional loading
print_section('8. Conditional Lazy Loading');
$json = json_encode([
    'required_data' => ['id' => 1, 'name' => 'Test'],
    'optional_data' => ['large' => str_repeat('data', 1000)],
]);

$lazy = new LazyJsonProxy($json);

// Load only if needed
$required = $lazy->get('required_data');
echo "Required data loaded: " . $required['name'] . "\n";

$loadOptional = false;
if ($loadOptional) {
    $optional = $lazy->get('optional_data');
}
echo "Optional data not loaded\n";

// Example 9: Lazy with transformations
print_section('9. Lazy Access with Safe Defaults');
$incompleteJson = '{"id": 1}';
$lazy = new LazyJsonProxy($incompleteJson);

$id = $lazy->get('id', 0);
$name = $lazy->get('name', 'Unknown');
$active = $lazy->get('active', true);

echo "ID: $id\n";
echo "Name: $name (using default)\n";
echo "Active: " . ($active ? 'yes' : 'no') . " (using default)\n";

// Example 10: Practical - Lazy API response handling
print_section('10. Practical: Lazy API Response Handling');

$apiResponses = [
    json_encode(['success' => true, 'data' => ['user_id' => 123, 'name' => 'Alice']]),
    json_encode(['success' => false, 'error' => 'Not found']),
    json_encode(['success' => true, 'data' => ['user_id' => 456, 'name' => 'Bob']]),
];

foreach ($apiResponses as $index => $response) {
    $lazy = new LazyJsonProxy($response);
    
    $success = $lazy->get('success');
    if ($success) {
        $userId = $lazy->get('data.user_id');
        $name = $lazy->get('data.name');
        echo "Response " . ($index + 1) . ": User $userId - $name\n";
    } else {
        $error = $lazy->get('error');
        echo "Response " . ($index + 1) . ": Error - $error\n";
    }
}

print_section('Examples Complete');
