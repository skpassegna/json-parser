<?php

/**
 * Iterator and IteratorAggregate
 *
 * Demonstrates how the Json class implements iteration interfaces,
 * allowing foreach loops and array operations.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Basic iteration
print_section('1. Basic Iteration with foreach');
$json = Json::create([
    'id' => 1,
    'name' => 'Product',
    'price' => 99.99,
    'active' => true,
]);

echo "Iterating through Json object:\n";
foreach ($json as $key => $value) {
    echo "  $key: ";
    if (is_array($value)) {
        echo "[array with " . count($value) . " items]\n";
    } else {
        echo "$value\n";
    }
}

// Example 2: Iterate over users
print_section('2. Iterate Over Collection');
$file = get_example_file('users.json');
if (file_exists($file)) {
    $json = Json::parse(file_get_contents($file));
    
    echo "Iterating through users:\n";
    $users = $json->get('users');
    foreach ($users as $index => $user) {
        echo ($index + 1) . ". " . $user['name'] . " (" . $user['email'] . ")\n";
    }
}

// Example 3: Count elements
print_section('3. Count Elements (Countable Interface)');
$json = Json::create([
    'item1' => 'first',
    'item2' => 'second',
    'item3' => 'third',
    'item4' => 'fourth',
]);

echo "Total items: " . count($json) . "\n";

// Count nested items
$json = Json::create([
    'users' => [
        ['id' => 1, 'name' => 'Alice'],
        ['id' => 2, 'name' => 'Bob'],
        ['id' => 3, 'name' => 'Charlie'],
    ],
]);

echo "Total users: " . count($json->get('users')) . "\n";

// Example 4: Iterate and filter
print_section('4. Iterate and Filter');
$json = Json::create([
    'products' => [
        ['name' => 'Laptop', 'price' => 999.99, 'available' => true],
        ['name' => 'Phone', 'price' => 599.99, 'available' => true],
        ['name' => 'Tablet', 'price' => 399.99, 'available' => false],
        ['name' => 'Monitor', 'price' => 299.99, 'available' => true],
    ],
]);

echo "Available products:\n";
$products = $json->get('products');
foreach ($products as $product) {
    if ($product['available']) {
        echo "  - " . $product['name'] . ": $" . $product['price'] . "\n";
    }
}

// Example 5: Use array_map with iteration
print_section('5. Map Operations Over Data');
$json = Json::create([
    'numbers' => [1, 2, 3, 4, 5],
]);

$numbers = $json->get('numbers');
$squared = array_map(fn($n) => $n * $n, $numbers);

echo "Original: " . implode(', ', $numbers) . "\n";
echo "Squared: " . implode(', ', $squared) . "\n";

// Example 6: Use array_filter with iteration
print_section('6. Filter Operations Over Data');
$json = Json::create([
    'items' => [
        ['id' => 1, 'status' => 'active'],
        ['id' => 2, 'status' => 'inactive'],
        ['id' => 3, 'status' => 'active'],
        ['id' => 4, 'status' => 'pending'],
        ['id' => 5, 'status' => 'active'],
    ],
]);

$items = $json->get('items');
$active = array_filter($items, fn($item) => $item['status'] === 'active');

echo "Active items: " . count($active) . "\n";
foreach ($active as $item) {
    echo "  - Item " . $item['id'] . ": " . $item['status'] . "\n";
}

// Example 7: Iterate with keys and values
print_section('7. Iterate with Keys and Values');
$json = Json::create([
    'config' => [
        'theme' => 'dark',
        'debug' => true,
        'timezone' => 'UTC',
        'language' => 'en',
    ],
]);

echo "Configuration settings:\n";
$config = $json->get('config');
foreach ($config as $setting => $value) {
    echo "  " . str_pad($setting, 15) . ": ";
    if (is_bool($value)) {
        echo ($value ? 'enabled' : 'disabled') . "\n";
    } else {
        echo "$value\n";
    }
}

// Example 8: Nested iteration
print_section('8. Nested Iteration');
$json = Json::create([
    'departments' => [
        [
            'name' => 'Engineering',
            'members' => ['Alice', 'Bob', 'Charlie'],
        ],
        [
            'name' => 'Sales',
            'members' => ['Diana', 'Eve'],
        ],
        [
            'name' => 'HR',
            'members' => ['Frank'],
        ],
    ],
]);

echo "Organization structure:\n";
$departments = $json->get('departments');
foreach ($departments as $dept) {
    echo "  " . $dept['name'] . ":\n";
    foreach ($dept['members'] as $member) {
        echo "    - $member\n";
    }
}

// Example 9: Collect values during iteration
print_section('9. Collect Values During Iteration');
$json = Json::create([
    'transactions' => [
        ['id' => 'TXN001', 'amount' => 100.00],
        ['id' => 'TXN002', 'amount' => 250.50],
        ['id' => 'TXN003', 'amount' => 75.25],
        ['id' => 'TXN004', 'amount' => 500.00],
    ],
]);

$transactions = $json->get('transactions');
$total = 0;
$count = 0;

foreach ($transactions as $txn) {
    $total += $txn['amount'];
    $count++;
}

echo "Total transactions: $count\n";
echo "Total amount: \$" . number_format($total, 2) . "\n";
echo "Average: \$" . number_format($total / $count, 2) . "\n";

// Example 10: Iterate and build new structure
print_section('10. Iterate and Build New Structure');
$json = Json::create([
    'items' => [
        ['id' => 1, 'name' => 'Item A', 'price' => 10],
        ['id' => 2, 'name' => 'Item B', 'price' => 20],
        ['id' => 3, 'name' => 'Item C', 'price' => 15],
    ],
]);

$result = Json::create([
    'total_items' => 0,
    'total_value' => 0,
    'items' => [],
]);

$items = $json->get('items');
foreach ($items as $item) {
    $total = $item['price'] * 2; // Hypothetical quantity
    
    $result->set('items.' . ($result->get('total_items')), [
        'id' => $item['id'],
        'name' => $item['name'],
        'total_value' => $total,
    ]);
    
    $result->set('total_items', $result->get('total_items') + 1);
    $result->set('total_value', $result->get('total_value') + $total);
}

print_json($result);

print_section('Examples Complete');
