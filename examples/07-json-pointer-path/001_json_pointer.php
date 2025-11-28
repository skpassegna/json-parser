<?php

/**
 * JSON Pointer (RFC 6902)
 *
 * Demonstrates using JSON Pointer syntax to access nested data
 * following RFC 6902 standard format.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Basic JSON Pointer access
print_section('1. Basic JSON Pointer Access');
$json = Json::create([
    'user' => [
        'name' => 'John',
        'email' => 'john@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'NYC',
        ],
    ],
]);

$name = $json->getPointer('/user/name');
$city = $json->getPointer('/user/address/city');
$email = $json->getPointer('/user/email');

echo "Name: $name\n";
echo "City: $city\n";
echo "Email: $email\n";

// Example 2: Array access with JSON Pointer
print_section('2. Array Element Access with Pointer');
$json = Json::create([
    'items' => [
        ['id' => 1, 'name' => 'Item 1'],
        ['id' => 2, 'name' => 'Item 2'],
        ['id' => 3, 'name' => 'Item 3'],
    ],
]);

$firstItem = $json->getPointer('/items/0/name');
$secondItem = $json->getPointer('/items/1/name');
$thirdItem = $json->getPointer('/items/2/name');

echo "First: $firstItem\n";
echo "Second: $secondItem\n";
echo "Third: $thirdItem\n";

// Example 3: Set values with JSON Pointer
print_section('3. Set Values with JSON Pointer');
$json = Json::create();

$json->setPointer('/user/name', 'Alice');
$json->setPointer('/user/age', 28);
$json->setPointer('/user/email', 'alice@example.com');
$json->setPointer('/user/verified', true);

print_json($json);

// Example 4: Check pointer existence
print_section('4. Check Pointer Existence');
$json = Json::create([
    'config' => [
        'database' => ['host' => 'localhost'],
    ],
]);

$exists1 = $json->hasPointer('/config/database/host');
$exists2 = $json->hasPointer('/config/database/port');
$exists3 = $json->hasPointer('/config/cache');

echo "Has /config/database/host: " . ($exists1 ? 'yes' : 'no') . "\n";
echo "Has /config/database/port: " . ($exists2 ? 'yes' : 'no') . "\n";
echo "Has /config/cache: " . ($exists3 ? 'yes' : 'no') . "\n";

// Example 5: Work with complex nested structure
print_section('5. Complex Nested Structure Access');
$json = Json::parse(file_get_contents(get_example_file('config.json')));

$dbDriver = $json->getPointer('/database/default');
$mysqlHost = $json->getPointer('/database/connections/mysql/host');
$mysqlPort = $json->getPointer('/database/connections/mysql/port');
$cacheDriver = $json->getPointer('/cache/default');

echo "Database driver: $dbDriver\n";
echo "MySQL host: $mysqlHost:$mysqlPort\n";
echo "Cache driver: $cacheDriver\n";

// Example 6: Working with deep nesting
print_section('6. Deep Nesting with Pointer');
$json = Json::create([
    'a' => [
        'b' => [
            'c' => [
                'd' => [
                    'e' => 'deeply nested value',
                ],
            ],
        ],
    ],
]);

$value = $json->getPointer('/a/b/c/d/e');
echo "Deep value: $value\n";

// Example 7: Pointer with special characters
print_section('7. Pointer with Special Characters (Escaping)');
$json = Json::create([
    'keys~with~tilde' => 'value1',
    'keys/with/slash' => 'value2',
    'normal_key' => 'value3',
]);

// For keys with special characters, might need escaping
// Standard pointer syntax
$value = $json->getPointer('/normal_key');
echo "Normal key: $value\n";

// Example 8: Update nested values
print_section('8. Update Values with Pointer');
$json = Json::create([
    'settings' => [
        'theme' => 'light',
        'notifications' => true,
    ],
]);

echo "Before: theme = " . $json->getPointer('/settings/theme') . "\n";
$json->setPointer('/settings/theme', 'dark');
echo "After: theme = " . $json->getPointer('/settings/theme') . "\n";

// Example 9: Batch operations with pointer
print_section('9. Batch Operations with Pointer');
$json = Json::create();

$pointers = [
    '/user/id' => 1,
    '/user/name' => 'Charlie',
    '/user/email' => 'charlie@example.com',
    '/metadata/created' => date('c'),
    '/metadata/version' => 1,
];

foreach ($pointers as $pointer => $value) {
    $json->setPointer($pointer, $value);
}

print_json($json);

// Example 10: Practical - API response path access
print_section('10. Practical: API Response Path Navigation');
$apiResponse = Json::create([
    'success' => true,
    'data' => [
        'user' => [
            'id' => 123,
            'profile' => [
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'avatar' => 'https://example.com/avatar.jpg',
            ],
            'settings' => [
                'notifications' => true,
                'privacy' => 'public',
            ],
        ],
        'token' => 'eyJhbGc...',
    ],
]);

$userId = $apiResponse->getPointer('/data/user/id');
$firstName = $apiResponse->getPointer('/data/user/profile/first_name');
$avatar = $apiResponse->getPointer('/data/user/profile/avatar');
$notifications = $apiResponse->getPointer('/data/user/settings/notifications');
$token = $apiResponse->getPointer('/data/token');

echo "User ID: $userId\n";
echo "Name: $firstName Doe\n";
echo "Avatar: $avatar\n";
echo "Notifications: " . ($notifications ? 'enabled' : 'disabled') . "\n";
echo "Token: " . substr($token, 0, 20) . "...\n";

print_section('Examples Complete');
