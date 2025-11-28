<?php

/**
 * Working with Sample Data Files
 *
 * Demonstrates loading and working with real sample data files.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Load users dataset
print_section('1. Load and Explore Users Dataset');
$file = get_example_file('users.json');
$json = Json::parse(file_get_contents($file));

$total = $json->get('total');
$users = $json->get('users');

echo "Total users in dataset: $total\n";
echo "Users in data array: " . count($users) . "\n";
echo "Active users: " . count(array_filter($users, fn($u) => $u['active'])) . "\n";

// Example 2: Load products dataset
print_section('2. Load and Explore Products Dataset');
$file = get_example_file('products.json');
$json = Json::parse(file_get_contents($file));

$store = $json->get('store.name');
$products = $json->get('store.products');

echo "Store: $store\n";
echo "Products: " . count($products) . "\n";

// Example 3: Load config dataset
print_section('3. Load and Explore Configuration Dataset');
$file = get_example_file('config.json');
$json = Json::parse(file_get_contents($file));

echo "App name: " . $json->get('app.name') . " v" . $json->get('app.version') . "\n";
echo "Database driver: " . $json->get('database.default') . "\n";

print_section('Examples Complete');
