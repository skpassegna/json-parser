<?php

/**
 * Advanced JSON Path and Pointer Queries
 *
 * Demonstrates complex querying patterns and combinations.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Complex nested queries
print_section('1. Complex Nested Queries');

$json = Json::parse(file_get_contents(get_example_file('users.json')));

// Get emails of active users
$activeUserEmails = [];
$activeUsers = $json->query('$.users[?(@.active == true)]');
foreach ($activeUsers as $user) {
    $activeUserEmails[] = $user['email'];
}

echo "Active user emails: " . implode(', ', $activeUserEmails) . "\n";

// Example 2: Aggregation during path access
print_section('2. Aggregate During Query');

$json = Json::parse(file_get_contents(get_example_file('products.json')));

$products = $json->query('$.store.products[*]');
$totalPrice = 0;
$count = 0;

foreach ($products as $product) {
    $totalPrice += $product['price'];
    $count++;
}

echo "Average price: \$" . number_format($totalPrice / $count, 2) . "\n";

// Example 3: Path validation
print_section('3. Path Validation');

$json = Json::create([
    'user' => ['profile' => ['name' => 'Alice']],
]);

$paths = [
    '/user/profile/name',
    '/user/profile/email',
    '/nonexistent/path',
];

foreach ($paths as $path) {
    $exists = $json->hasPointer($path);
    echo "$path: " . ($exists ? 'exists' : 'not found') . "\n";
}

// Example 4: Pointer array access
print_section('4. Pointer with Array Indices');

$json = Json::create([
    'items' => [
        ['id' => 1, 'name' => 'Item 1'],
        ['id' => 2, 'name' => 'Item 2'],
        ['id' => 3, 'name' => 'Item 3'],
    ],
]);

for ($i = 0; $i < 3; $i++) {
    $name = $json->getPointer("/items/$i/name");
    echo "Item $i: $name\n";
}

print_section('Examples Complete');
