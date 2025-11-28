<?php

/**
 * Builder Pattern with Fluent Interface
 *
 * Demonstrates using the fluent API to build complex data structures
 * using the builder pattern.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Build user incrementally
print_section('1. Incremental User Building');
$user = Json::create()
    ->set('id', 101)
    ->set('username', 'john_doe')
    ->set('email', 'john@example.com');

// Add more details
$user->set('first_name', 'John')
    ->set('last_name', 'Doe')
    ->set('age', 30);

// Add profile
$user->set('profile.bio', 'Software developer')
    ->set('profile.website', 'https://johndoe.dev')
    ->set('profile.location', 'San Francisco');

print_json($user);

// Example 2: Build API response dynamically
print_section('2. Build API Response Dynamically');
$response = Json::create();

// Start with base structure
$response->set('success', true);
$response->set('message', 'Request processed successfully');

// Add data conditionally
$response->set('data.user_id', 42);
$response->set('data.timestamp', date('c'));
$response->set('data.items_count', 10);

// Add metadata
$response->set('meta.version', '1.0');
$response->set('meta.request_id', uniqid());

print_json($response);

// Example 3: Build configuration from components
print_section('3. Build Configuration from Components');
$config = Json::create();

// Database configuration
$config->set('database.driver', 'mysql')
    ->set('database.host', 'db.example.com')
    ->set('database.port', 3306)
    ->set('database.name', 'production');

// Cache configuration
$config->set('cache.driver', 'redis')
    ->set('cache.host', 'cache.example.com')
    ->set('cache.port', 6379)
    ->set('cache.ttl', 3600);

// Log configuration
$config->set('logging.level', 'info')
    ->set('logging.channels.0', 'file')
    ->set('logging.channels.1', 'syslog');

print_json($config);

// Example 4: Build product catalog
print_section('4. Build Product Catalog');
$catalog = Json::create()
    ->set('store_id', 1)
    ->set('store_name', 'Tech Store')
    ->set('currency', 'USD');

// Add products incrementally
$catalog->set('products.0.id', 101)
    ->set('products.0.name', 'Laptop')
    ->set('products.0.price', 999.99)
    ->set('products.0.stock', 50);

$catalog->set('products.1.id', 102)
    ->set('products.1.name', 'Monitor')
    ->set('products.1.price', 299.99)
    ->set('products.1.stock', 100);

$catalog->set('products.2.id', 103)
    ->set('products.2.name', 'Keyboard')
    ->set('products.2.price', 89.99)
    ->set('products.2.stock', 200);

// Add metadata
$catalog->set('updated_at', date('c'))
    ->set('total_products', 3)
    ->set('version', 1);

print_json($catalog);

// Example 5: Multi-step error response builder
print_section('5. Build Error Response');
$error = Json::create()
    ->set('success', false)
    ->set('status_code', 400)
    ->set('error.code', 'VALIDATION_ERROR')
    ->set('error.message', 'Input validation failed')
    ->set('error.details.field', 'email')
    ->set('error.details.issue', 'Invalid email format')
    ->set('error.timestamp', date('c'))
    ->set('error.trace_id', 'tr_' . uniqid());

print_json($error);

// Example 6: Build complex nested structure
print_section('6. Build Complex Organization Structure');
$org = Json::create()
    ->set('id', 1)
    ->set('name', 'Tech Corp')
    ->set('founded', 2020)
    ->set('headquarters.city', 'San Francisco')
    ->set('headquarters.country', 'USA');

// Add departments
$org->set('departments.0.id', 1)
    ->set('departments.0.name', 'Engineering')
    ->set('departments.0.head', 'Alice')
    ->set('departments.0.team_size', 50);

$org->set('departments.1.id', 2)
    ->set('departments.1.name', 'Sales')
    ->set('departments.1.head', 'Bob')
    ->set('departments.1.team_size', 30);

// Add fiscal info
$org->set('fiscal.revenue_2024', 5000000)
    ->set('fiscal.employees', 200)
    ->set('fiscal.public', false);

print_json($org);

// Example 7: Builder with state management
print_section('7. Builder with State Tracking');
$builder = Json::create();
$fieldCount = 0;

$builder->set('timestamp', date('c'));
$fieldCount++;

$builder->set('user.id', 1)->set('user.name', 'Test');
$fieldCount += 2;

$builder->set('metadata.version', 1)->set('metadata.fields', $fieldCount);

print_json($builder);
echo "Total fields set: " . $builder->get('metadata.fields') . "\n";

// Example 8: Reusable builder base
print_section('8. Reusable Base Builder');
// Create a base template
$baseTemplate = Json::create()
    ->set('version', '1.0')
    ->set('created_at', date('c'));

// Create instance 1
$instance1 = Json::create($baseTemplate->toArray())
    ->set('id', 1)
    ->set('title', 'First Item')
    ->set('data.value', 100);

// Create instance 2
$instance2 = Json::create($baseTemplate->toArray())
    ->set('id', 2)
    ->set('title', 'Second Item')
    ->set('data.value', 200);

echo "Instance 1:\n";
print_json($instance1);
echo "\nInstance 2:\n";
print_json($instance2);

// Example 9: Build with conditional fields
print_section('9. Conditional Field Addition');
$premium = true;
$data = Json::create()
    ->set('user_id', 123)
    ->set('name', 'Premium User');

if ($premium) {
    $data->set('subscription.level', 'premium')
        ->set('subscription.expires', date('Y-m-d', strtotime('+1 year')))
        ->set('features.0', 'advanced_analytics')
        ->set('features.1', 'priority_support')
        ->set('features.2', 'api_access');
}

print_json($data);

// Example 10: Build then validate structure
print_section('10. Build and Verify');
$record = Json::create()
    ->set('record_id', 'REC-2025-001')
    ->set('status', 'complete')
    ->set('fields.title', 'Important Record')
    ->set('fields.description', 'This is a test record')
    ->set('timestamps.created', date('c'))
    ->set('timestamps.modified', date('c'));

echo "Built record structure:\n";
$array = $record->toArray();
echo "Total top-level keys: " . count($array) . "\n";
echo "Status: " . $record->get('status') . "\n";
echo "Created: " . $record->get('timestamps.created') . "\n";

print_section('Examples Complete');
