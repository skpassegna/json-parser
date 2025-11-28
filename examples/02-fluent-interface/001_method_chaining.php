<?php

/**
 * Fluent Interface and Method Chaining
 *
 * Demonstrates the fluent API for building and modifying JSON structures
 * through chainable method calls.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Basic chaining with set
print_section('1. Basic Method Chaining with Set');
$json = Json::create()
    ->set('id', 1)
    ->set('name', 'Product')
    ->set('price', 99.99)
    ->set('active', true);

print_json($json);

// Example 2: Nested set operations
print_section('2. Build Nested Structures');
$json = Json::create()
    ->set('user.id', 1)
    ->set('user.name', 'John Doe')
    ->set('user.email', 'john@example.com')
    ->set('user.profile.bio', 'A PHP developer')
    ->set('user.profile.location', 'San Francisco')
    ->set('user.profile.joined', '2023-01-15');

print_json($json);

// Example 3: Add multiple related data
print_section('3. Build User Profile with Multiple Chains');
$user = Json::create()
    ->set('name', 'Alice')
    ->set('email', 'alice@example.com')
    ->set('roles.0', 'admin')
    ->set('roles.1', 'moderator')
    ->set('roles.2', 'user')
    ->set('settings.theme', 'dark')
    ->set('settings.notifications', true)
    ->set('settings.language', 'en');

print_json($user);

// Example 4: Build API response structure
print_section('4. Build API Response');
$response = Json::create()
    ->set('success', true)
    ->set('status_code', 200)
    ->set('message', 'Operation completed')
    ->set('data.user_id', 123)
    ->set('data.created_at', date('c'))
    ->set('data.items_processed', 42);

print_json($response);

// Example 5: Chain with parsing
print_section('5. Parse and Chain Modifications');
$baseJson = Json::parse('{"name": "Product", "price": 50}');
$modified = $baseJson
    ->set('discount', 10)
    ->set('final_price', 40)
    ->set('in_stock', true)
    ->set('sku', 'PRD-001');

print_json($modified);

// Example 6: Build list with items
print_section('6. Build List with Multiple Items');
$list = Json::create()
    ->set('title', 'Shopping List')
    ->set('items.0.id', 1)
    ->set('items.0.name', 'Milk')
    ->set('items.0.quantity', 2)
    ->set('items.1.id', 2)
    ->set('items.1.name', 'Bread')
    ->set('items.1.quantity', 1)
    ->set('items.2.id', 3)
    ->set('items.2.name', 'Eggs')
    ->set('items.2.quantity', 12);

print_json($list);

// Example 7: Build and verify
print_section('7. Chain Build Operations');
$config = Json::create()
    ->set('database.host', 'localhost')
    ->set('database.port', 5432)
    ->set('database.name', 'myapp')
    ->set('cache.enabled', true)
    ->set('cache.ttl', 3600)
    ->set('logging.level', 'info');

echo "Config: " . $config->toString(JSON_PRETTY_PRINT) . "\n";
echo "Database host: " . $config->get('database.host') . "\n";
echo "Cache enabled: " . ($config->get('cache.enabled') ? 'yes' : 'no') . "\n";

// Example 8: Create from array then chain
print_section('8. Create from Array and Chain');
$baseData = [
    'id' => 1,
    'name' => 'Original',
    'status' => 'pending',
];

$enhanced = Json::create($baseData)
    ->set('updated_at', date('c'))
    ->set('version', 2)
    ->set('tags.0', 'php')
    ->set('tags.1', 'json');

print_json($enhanced);

// Example 9: Multiple chains with intermediate values
print_section('9. Chain with Readable Intermediate Steps');
$report = Json::create()
    ->set('title', 'Monthly Report')
    ->set('period', 'January 2025')
    ->set('metrics.users', 1500)
    ->set('metrics.revenue', 50000)
    ->set('metrics.conversion', 0.032)
    ->set('generated_at', date('c'))
    ->set('format_version', 1);

print_json($report);

// Example 10: Complex nested structure in one chain
print_section('10. Complex Nested Structure');
$order = Json::create()
    ->set('order_id', 'ORD-12345')
    ->set('customer.name', 'Bob Smith')
    ->set('customer.email', 'bob@example.com')
    ->set('items.0.product', 'Laptop')
    ->set('items.0.quantity', 1)
    ->set('items.0.price', 999.99)
    ->set('items.1.product', 'Mouse')
    ->set('items.1.quantity', 2)
    ->set('items.1.price', 29.99)
    ->set('shipping.address', '123 Main St')
    ->set('shipping.city', 'New York')
    ->set('shipping.zip', '10001')
    ->set('total', 1059.97)
    ->set('status', 'processing');

print_json($order);

print_section('Examples Complete');
