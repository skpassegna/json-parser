<?php

/**
 * ArrayAccess Interface Implementation
 *
 * Demonstrates how the Json class implements ArrayAccess,
 * allowing array-like access to JSON data.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Access like an array - isset/unset
print_section('1. Array-like Access with isset/unset');
$json = Json::create([
    'id' => 1,
    'name' => 'Product',
    'price' => 99.99,
]);

echo "Using array access [key]:\n";
echo "ID: " . $json['id'] . "\n";
echo "Name: " . $json['name'] . "\n";
echo "Price: " . $json['price'] . "\n";

echo "\nCheck if key exists: " . (isset($json['id']) ? 'yes' : 'no') . "\n";
echo "Check if missing key exists: " . (isset($json['missing']) ? 'yes' : 'no') . "\n";

// Example 2: Set values using array syntax
print_section('2. Set Values with Array Syntax');
$json = Json::create();

$json['user'] = 'alice';
$json['email'] = 'alice@example.com';
$json['active'] = true;

print_json($json);

// Example 3: Modify nested values
print_section('3. Modify Array Elements');
$json = Json::create([
    'config' => [
        'debug' => false,
        'timeout' => 30,
    ],
]);

echo "Before: debug = " . ($json['config']['debug'] ? 'true' : 'false') . "\n";

$config = $json['config'];
$config['debug'] = true;
$json['config'] = $config;

echo "After: debug = " . ($json['config']['debug'] ? 'true' : 'false') . "\n";

// Example 4: Unset/Remove using unset()
print_section('4. Remove Elements with unset()');
$json = Json::create([
    'field1' => 'value1',
    'field2' => 'value2',
    'field3' => 'value3',
]);

echo "Before unset:\n";
var_export($json->toArray());

unset($json['field2']);

echo "\nAfter unset('field2'):\n";
var_export($json->toArray());

// Example 5: Work with arrays at top level
print_section('5. Work with Top-Level Arrays');
$json = Json::create([
    'items' => [
        'item1' => 'first',
        'item2' => 'second',
        'item3' => 'third',
    ],
]);

echo "Items array:\n";
foreach ($json['items'] as $key => $value) {
    echo "  $key: $value\n";
}

// Example 6: Modify array elements
print_section('6. Modify Array Elements via ArrayAccess');
$json = Json::create([
    'numbers' => [10, 20, 30, 40],
]);

// Access and show
echo "Original: " . implode(', ', $json['numbers']) . "\n";

// Modify individual element
$numbers = $json['numbers'];
$numbers[1] = 25;
$json['numbers'] = $numbers;

echo "Modified: " . implode(', ', $json['numbers']) . "\n";

// Example 7: Compare array access with dot notation
print_section('7. Compare Array Access vs Dot Notation');
$json = Json::create(['user' => ['name' => 'Bob', 'age' => 25]]);

// Both work for simple access
$name1 = $json->get('user.name');
$nameData = $json['user'];
$name2 = $nameData['name'] ?? null;

echo "Using get() with dot notation: $name1\n";
echo "Using array access: $name2\n";

// Example 8: Check type of access
print_section('8. Type of Access Demonstration');
$json = Json::create([
    'status' => 'active',
    'config' => [
        'theme' => 'dark',
        'lang' => 'en',
    ],
]);

// Simple access
echo "Simple access: " . $json['status'] . "\n";

// Array access for nested
echo "Nested via array: " . $json['config']['theme'] . "\n";

// Can also use isset on nested
echo "Nested via isset: " . (isset($json['config']['lang']) ? $json['config']['lang'] : 'not found') . "\n";

// Example 9: Build using array syntax
print_section('9. Build Structure with Array Syntax');
$data = Json::create();

// Add top-level items
$data['id'] = 1;
$data['title'] = 'Article';

// Add config array
$data['config'] = [
    'visibility' => 'public',
    'comments' => true,
];

// Add meta
$data['meta'] = [
    'author' => 'John',
    'date' => date('Y-m-d'),
];

print_json($data);

// Example 10: Practical - Data transformation
print_section('10. Practical: Data Transformation');
$rawData = Json::create([
    'users' => [
        ['id' => 1, 'name' => 'Alice'],
        ['id' => 2, 'name' => 'Bob'],
    ],
]);

// Add computed data
$users = $rawData['users'];
$rawData['user_count'] = count($users);
$rawData['processed_at'] = date('c');
$rawData['status'] = 'ready';

print_json($rawData);

print_section('Examples Complete');
