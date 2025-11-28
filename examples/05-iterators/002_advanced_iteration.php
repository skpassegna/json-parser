<?php

/**
 * Advanced Iteration Patterns
 *
 * Demonstrates complex iteration scenarios including nested loops,
 * filtering, and aggregation during iteration.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Recursive iteration
print_section('1. Recursive Data Iteration');

$json = Json::create([
    'level1' => [
        'level2' => [
            'level3' => ['value' => 'deep'],
        ],
    ],
]);

function iterateRecursive($data, $prefix = '') {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $path = $prefix ? "$prefix.$key" : $key;
            if (is_array($value) && !empty($value)) {
                iterateRecursive($value, $path);
            } else {
                echo "  $path = ";
                echo is_array($value) ? '[array]' : $value;
                echo "\n";
            }
        }
    }
}

echo "Recursive iteration:\n";
iterateRecursive($json->toArray());

// Example 2: Key-value pairing
print_section('2. Key-Value Pairing');

$config = Json::create([
    'database' => 'mysql',
    'host' => 'localhost',
    'port' => 3306,
    'cache' => 'redis',
]);

foreach ($config->toArray() as $key => $value) {
    echo str_pad($key, 15) . " => " . var_export($value, true) . "\n";
}

// Example 3: Filtering during iteration
print_section('3. Filter During Iteration');

$products = [
    ['name' => 'Laptop', 'price' => 1000, 'stock' => 5],
    ['name' => 'Mouse', 'price' => 25, 'stock' => 0],
    ['name' => 'Keyboard', 'price' => 75, 'stock' => 10],
    ['name' => 'Monitor', 'price' => 300, 'stock' => 0],
];

$inStock = 0;
$outOfStock = 0;
$totalValue = 0;

foreach ($products as $product) {
    if ($product['stock'] > 0) {
        $inStock++;
        $totalValue += $product['price'] * $product['stock'];
    } else {
        $outOfStock++;
    }
}

echo "In stock: $inStock\n";
echo "Out of stock: $outOfStock\n";
echo "Total inventory value: \$" . number_format($totalValue, 2) . "\n";

// Example 4: Build new structure during iteration
print_section('4. Transform During Iteration');

$input = [
    ['id' => 1, 'value' => 10],
    ['id' => 2, 'value' => 20],
    ['id' => 3, 'value' => 30],
];

$output = Json::create(['items' => [], 'total' => 0]);

foreach ($input as $index => $item) {
    $output->set("items.$index", [
        'id' => $item['id'],
        'value' => $item['value'],
        'doubled' => $item['value'] * 2,
    ]);
}

$total = array_sum(array_column($input, 'value'));
$output->set('total', $total);

print_json($output);

// Example 5: Group by during iteration
print_section('5. Group During Iteration');

$items = [
    ['category' => 'A', 'value' => 10],
    ['category' => 'B', 'value' => 20],
    ['category' => 'A', 'value' => 15],
    ['category' => 'C', 'value' => 30],
    ['category' => 'B', 'value' => 25],
];

$grouped = [];
foreach ($items as $item) {
    $cat = $item['category'];
    if (!isset($grouped[$cat])) {
        $grouped[$cat] = [];
    }
    $grouped[$cat][] = $item;
}

echo "Grouped data:\n";
foreach ($grouped as $category => $items) {
    echo "  $category: " . count($items) . " items\n";
}

print_section('Examples Complete');
