<?php

/**
 * Caching and Performance Optimization
 *
 * Demonstrates caching strategies for JSON data, including warm-up,
 * invalidation, and performance optimization techniques.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;
use Skpassegna\Json\Cache\MemoryStore;

// Example 1: Basic cache warm-up
print_section('1. Cache Warm-Up');
$cache = new MemoryStore();

echo "Warming up cache...\n";

// Pre-load commonly accessed data
$userConfig = Json::create([
    'id' => 1,
    'roles' => ['admin', 'user'],
    'permissions' => ['read', 'write', 'delete'],
]);

$cache->set('user_config:1', $userConfig->toArray());
echo "Cached user_config:1\n";

// Load from cache
$cached = $cache->get('user_config:1');
if ($cached !== null) {
    echo "Retrieved from cache: " . $cached['roles'][0] . "\n";
}

// Example 2: Query result caching
print_section('2. Query Result Caching');
$cache = new MemoryStore();

function getUsersFromDb() {
    // Simulate expensive database query
    usleep(100000); // 100ms
    return [
        ['id' => 1, 'name' => 'Alice'],
        ['id' => 2, 'name' => 'Bob'],
        ['id' => 3, 'name' => 'Charlie'],
    ];
}

// First access - cache miss
$start = microtime(true);
$users = $cache->get('users');
if ($users === null) {
    $users = getUsersFromDb();
    $cache->set('users', $users);
    echo "Cache miss - Fetched from DB\n";
}
$time1 = microtime(true) - $start;
echo "First access time: " . ($time1 * 1000) . " ms\n";

// Second access - cache hit
$start = microtime(true);
$users = $cache->get('users');
$time2 = microtime(true) - $start;
echo "Second access time: " . ($time2 * 1000) . " ms\n";
echo "Speedup: " . number_format($time1 / $time2, 1) . "x\n";

// Example 3: Cache with TTL (simulation)
print_section('3. Time-Based Cache Invalidation');
$cache = new MemoryStore();

$data = ['timestamp' => time(), 'value' => 'cached data'];
$ttl = 2; // 2 seconds

echo "Storing data with TTL of $ttl seconds\n";
$cache->set('session_data', $data);

echo "Data available immediately\n";
echo "Value: " . $cache->get('session_data')['value'] . "\n";

// Simulate TTL expiration
sleep(1);
echo "After 1 second: data still available\n";

echo "Note: MemoryStore doesn't auto-expire, but shows cache structure\n";

// Example 4: Cache invalidation patterns
print_section('4. Cache Invalidation');
$cache = new MemoryStore();

// Store multiple related items
$cache->set('user:1', ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com']);
$cache->set('user:1:profile', ['bio' => 'Developer']);
$cache->set('user:1:settings', ['theme' => 'dark']);

echo "Cached user and related data\n";

// Invalidate user data
$cache->delete('user:1');
$cache->delete('user:1:profile');
$cache->delete('user:1:settings');

echo "Cache invalidated\n";
echo "User data available: " . (($cache->get('user:1') !== null) ? 'yes' : 'no') . "\n";

// Example 5: Selective caching
print_section('5. Selective Data Caching');
$cache = new MemoryStore();

$json = Json::parse(file_get_contents(get_example_file('users.json')));

// Cache only active users
$activeUsers = array_filter($json->get('users'), fn($u) => $u['active']);
$cache->set('active_users', $activeUsers);

echo "Cached " . count($activeUsers) . " active users\n";
echo "Cache size: " . sizeof($activeUsers) . " items\n";

// Example 6: Hierarchical cache keys
print_section('6. Hierarchical Cache Keys');
$cache = new MemoryStore();

// Use namespaced keys
$cacheKeys = [
    'user:1:profile' => ['name' => 'Alice', 'age' => 28],
    'user:1:permissions' => ['read', 'write'],
    'user:2:profile' => ['name' => 'Bob', 'age' => 35],
    'user:2:permissions' => ['read'],
];

echo "Setting hierarchical cache keys:\n";
foreach ($cacheKeys as $key => $value) {
    $cache->set($key, $value);
    echo "  - $key\n";
}

// Retrieve by pattern
$user1Profile = $cache->get('user:1:profile');
echo "\nRetrieved user:1:profile: " . $user1Profile['name'] . "\n";

// Example 7: Cache warming strategy
print_section('7. Cache Warming Strategy');
$cache = new MemoryStore();

function warmupApplicationCache($cache) {
    // Warm up configuration
    $config = [
        'app_name' => 'MyApp',
        'version' => '1.0.0',
        'features' => ['auth', 'api', 'dashboard'],
    ];
    $cache->set('config:app', $config);
    
    // Warm up localization
    $locale = [
        'en' => ['hello' => 'Hello', 'goodbye' => 'Goodbye'],
        'es' => ['hello' => 'Hola', 'goodbye' => 'Adiós'],
    ];
    $cache->set('config:locale', $locale);
    
    // Warm up permissions
    $permissions = [
        'admin' => ['*'],
        'user' => ['read', 'write'],
        'guest' => ['read'],
    ];
    $cache->set('config:permissions', $permissions);
}

echo "Warming up application cache...\n";
warmupApplicationCache($cache);

echo "Config app: " . $cache->get('config:app')['app_name'] . "\n";
echo "Config locale: " . implode(', ', array_keys($cache->get('config:locale'))) . "\n";
echo "Config permissions: " . count($cache->get('config:permissions')) . " roles\n";

// Example 8: Cache statistics
print_section('8. Cache Statistics');
$cache = new MemoryStore();

// Simulate cache usage
$operations = 0;
$hits = 0;
$misses = 0;

for ($i = 1; $i <= 10; $i++) {
    $key = 'item:' . ($i % 5);
    $operations++;
    
    if ($cache->get($key) !== null) {
        $hits++;
    } else {
        $cache->set($key, ['id' => $i, 'value' => 'data']);
        $misses++;
    }
}

echo "Total operations: $operations\n";
echo "Cache hits: $hits\n";
echo "Cache misses: $misses\n";
echo "Hit rate: " . number_format(($hits / $operations) * 100, 1) . "%\n";

// Example 9: Data transformation caching
print_section('9. Transformed Data Caching');
$cache = new MemoryStore();

// Cache original
$original = ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com'];
$cache->set('user:1:raw', $original);

// Cache transformed format
$transformed = [
    'id' => $original['id'],
    'display_name' => strtoupper($original['name']),
    'contact' => $original['email'],
];
$cache->set('user:1:display', $transformed);

echo "Raw data cached:\n";
var_export($cache->get('user:1:raw'));

echo "\nTransformed data cached:\n";
var_export($cache->get('user:1:display'));

// Example 10: Practical - Cache invalidation on update
print_section('10. Practical: Update with Cache Invalidation');
$cache = new MemoryStore();

// Initial data
$user = ['id' => 1, 'name' => 'Alice', 'email' => 'alice@old.com'];
$cache->set('user:1', $user);

echo "Initial cache: " . $cache->get('user:1')['email'] . "\n";

// Update operation
$user['email'] = 'alice@new.com';
$user['updated_at'] = date('c');

// Invalidate cache and update
$cache->delete('user:1');
$cache->set('user:1', $user);

echo "After update: " . $cache->get('user:1')['email'] . "\n";

print_section('Examples Complete');
