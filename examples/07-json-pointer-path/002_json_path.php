<?php

/**
 * JSON Path Querying
 *
 * Demonstrates using JSONPath expressions to query and filter
 * complex JSON structures.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Basic path queries
print_section('1. Basic JSONPath Queries');
$json = Json::parse(file_get_contents(get_example_file('products.json')));

// Get all product names
$allProducts = $json->query('$.store.products[*].name');
echo "All products: " . implode(', ', $allProducts) . "\n";

// Get all prices
$prices = $json->query('$.store.products[*].price');
echo "All prices: \$" . implode(', $', $prices) . "\n";

// Example 2: Filter with conditions
print_section('2. Filter Products by Price');
$json = Json::parse(file_get_contents(get_example_file('products.json')));

// Products cheaper than 500
$cheapProducts = $json->query('$.store.products[?(@.price < 500)]');
echo "Products under \$500:\n";
foreach ($cheapProducts as $product) {
    echo "  - " . $product['name'] . ": \$" . $product['price'] . "\n";
}

// Example 3: Query users
print_section('3. Query Users from Collection');
$json = Json::parse(file_get_contents(get_example_file('users.json')));

// All active users
$activeUsers = $json->query('$.users[?(@.active == true)]');
echo "Active users: " . count($activeUsers) . "\n";
foreach ($activeUsers as $user) {
    echo "  - " . $user['name'] . " (" . $user['email'] . ")\n";
}

// Example 4: Nested queries
print_section('4. Query Nested Properties');
$json = Json::parse(file_get_contents(get_example_file('users.json')));

// Get preferences for all active users
$preferences = $json->query('$.users[?(@.active == true)].metadata.preferences.theme');
echo "Active users themes: " . implode(', ', $preferences) . "\n";

// Example 5: Array filtering
print_section('5. Complex Array Filtering');
$json = Json::create([
    'store' => [
        'inventory' => [
            ['sku' => 'SKU001', 'quantity' => 50, 'price' => 100],
            ['sku' => 'SKU002', 'quantity' => 0, 'price' => 200],
            ['sku' => 'SKU003', 'quantity' => 25, 'price' => 150],
            ['sku' => 'SKU004', 'quantity' => 75, 'price' => 120],
        ],
    ],
]);

// Items in stock
$inStock = $json->query('$.store.inventory[?(@.quantity > 0)]');
echo "Items in stock: " . count($inStock) . "\n";
foreach ($inStock as $item) {
    echo "  - " . $item['sku'] . ": " . $item['quantity'] . " units @ \$" . $item['price'] . "\n";
}

// Example 6: Wildcard queries
print_section('6. Wildcard Queries');
$json = Json::create([
    'departments' => [
        [
            'name' => 'Engineering',
            'members' => ['Alice', 'Bob'],
            'budget' => 500000,
        ],
        [
            'name' => 'Sales',
            'members' => ['Charlie', 'Diana', 'Eve'],
            'budget' => 300000,
        ],
        [
            'name' => 'HR',
            'members' => ['Frank'],
            'budget' => 150000,
        ],
    ],
]);

// Get all department names
$deptNames = $json->query('$.departments[*].name');
echo "Departments: " . implode(', ', $deptNames) . "\n";

// Get all budgets
$budgets = $json->query('$.departments[*].budget');
echo "Total budget: \$" . number_format(array_sum($budgets), 0) . "\n";

// Example 7: Recursive descent
print_section('7. Recursive Descent Queries');
$json = Json::create([
    'company' => [
        'name' => 'TechCorp',
        'employees' => [
            [
                'id' => 1,
                'name' => 'Alice',
                'skills' => ['PHP', 'JavaScript'],
            ],
            [
                'id' => 2,
                'name' => 'Bob',
                'skills' => ['Python', 'Java'],
            ],
        ],
    ],
]);

// Find all skills mentioned anywhere
$allSkills = $json->query('$..skills[*]');
$flatSkills = array_merge(...$allSkills);
echo "All skills used: " . implode(', ', array_unique($flatSkills)) . "\n";

// Example 8: Multiple level filtering
print_section('8. Multi-Level Filtering');
$json = Json::parse(file_get_contents(get_example_file('users.json')));

// Find all admins that are active
$admins = $json->query('$.users[?(@.active == true && @.roles[*] == "admin")]');
echo "Active admins: " . count($admins) . "\n";

// Example 9: Get array indices
print_section('9. Get Specific Array Elements');
$json = Json::parse(file_get_contents(get_example_file('products.json')));

// First product
$first = $json->query('$.store.products[0]');
echo "First product: " . ($first[0]['name'] ?? 'N/A') . "\n";

// Last product
$last = $json->query('$.store.products[-1]');
echo "Last product: " . ($last[0]['name'] ?? 'N/A') . "\n";

// Example 10: Practical - Search and aggregate
print_section('10. Practical: Search and Aggregate');
$json = Json::parse(file_get_contents(get_example_file('products.json')));

// Find all electronics
$electronics = $json->query('$.store.products[?(@.category == "electronics")]');
echo "Electronics found: " . count($electronics) . "\n";

// Calculate average price
if (!empty($electronics)) {
    $prices = array_map(fn($p) => $p['price'], $electronics);
    $avg = array_sum($prices) / count($prices);
    echo "Average price: \$" . number_format($avg, 2) . "\n";
    
    foreach ($electronics as $product) {
        echo "  - " . $product['name'] . ": \$" . $product['price'] . "\n";
    }
}

print_section('Examples Complete');
