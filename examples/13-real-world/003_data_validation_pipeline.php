<?php

/**
 * Real-World: Data Validation Pipeline
 *
 * Demonstrates building a validation pipeline for JSON data,
 * checking types, constraints, and business rules.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Simple field validation
print_section('1. Simple Field Validation');

$userData = Json::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => 30,
]);

$errors = [];

// Validate required fields
if (!$userData->has('name') || empty($userData->get('name'))) {
    $errors[] = 'Name is required';
}

if (!$userData->has('email') || empty($userData->get('email'))) {
    $errors[] = 'Email is required';
}

// Validate field types
if ($userData->has('age') && !is_int($userData->get('age'))) {
    $errors[] = 'Age must be an integer';
}

// Validate constraints
if ($userData->get('age') < 18) {
    $errors[] = 'Must be at least 18 years old';
}

echo "Validation result:\n";
if (empty($errors)) {
    echo "✓ All validations passed\n";
} else {
    echo "✗ Validation errors:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}

// Example 2: Email format validation
print_section('2. Email Format Validation');

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

$emails = [
    'valid@example.com',
    'invalid.email',
    'user@domain.co.uk',
    'test@',
];

echo "Email validation:\n";
foreach ($emails as $email) {
    $valid = validateEmail($email) ? '✓' : '✗';
    echo "  $valid $email\n";
}

// Example 3: Product data validation
print_section('3. Complex Product Validation');

$product = Json::create([
    'id' => 1,
    'name' => 'Laptop',
    'price' => 999.99,
    'stock' => 50,
    'category' => 'electronics',
    'tags' => ['computer', 'portable'],
]);

function validateProduct($product) {
    $errors = [];
    
    // Required fields
    foreach (['id', 'name', 'price', 'stock'] as $field) {
        if (!$product->has($field)) {
            $errors[] = "Missing required field: $field";
        }
    }
    
    // Type checks
    if ($product->has('id') && !is_int($product->get('id'))) {
        $errors[] = "ID must be an integer";
    }
    
    if ($product->has('price') && !is_numeric($product->get('price'))) {
        $errors[] = "Price must be numeric";
    }
    
    // Constraint checks
    if ($product->has('price') && $product->get('price') <= 0) {
        $errors[] = "Price must be greater than 0";
    }
    
    if ($product->has('stock') && $product->get('stock') < 0) {
        $errors[] = "Stock cannot be negative";
    }
    
    // String length
    if ($product->has('name')) {
        $name = $product->get('name');
        if (strlen($name) < 3 || strlen($name) > 100) {
            $errors[] = "Product name must be between 3 and 100 characters";
        }
    }
    
    return $errors;
}

$errors = validateProduct($product);
echo "Product validation:\n";
if (empty($errors)) {
    echo "✓ Product is valid\n";
} else {
    echo "✗ Errors:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}

// Example 4: Nested data validation
print_section('4. Nested Data Validation');

$order = Json::create([
    'id' => 'ORD-001',
    'customer' => [
        'name' => 'Alice',
        'email' => 'alice@example.com',
    ],
    'items' => [
        ['product_id' => 1, 'quantity' => 2, 'price' => 50.00],
        ['product_id' => 2, 'quantity' => 1, 'price' => 100.00],
    ],
    'total' => 200.00,
]);

$errors = [];

// Validate customer
$customer = $order->get('customer');
if (!$customer || empty($customer['name'])) {
    $errors[] = "Customer name is required";
}
if (!$customer || !validateEmail($customer['email'])) {
    $errors[] = "Customer email is invalid";
}

// Validate items
$items = $order->get('items');
if (empty($items)) {
    $errors[] = "Order must have at least one item";
}

foreach ($items as $index => $item) {
    if ($item['quantity'] <= 0) {
        $errors[] = "Item $index quantity must be greater than 0";
    }
    if ($item['price'] < 0) {
        $errors[] = "Item $index price cannot be negative";
    }
}

// Validate total
$calculatedTotal = 0;
foreach ($items as $item) {
    $calculatedTotal += $item['quantity'] * $item['price'];
}

if ($order->get('total') !== $calculatedTotal) {
    $errors[] = "Total amount does not match items";
}

echo "Order validation:\n";
if (empty($errors)) {
    echo "✓ Order is valid\n";
} else {
    echo "✗ Errors:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}

// Example 5: Enum validation
print_section('5. Enum Value Validation');

$statuses = ['pending', 'processing', 'completed', 'cancelled'];
$validatedEntities = [];

$entities = [
    ['id' => 1, 'status' => 'completed'],
    ['id' => 2, 'status' => 'invalid_status'],
    ['id' => 3, 'status' => 'pending'],
];

foreach ($entities as $entity) {
    $json = Json::create($entity);
    $status = $json->get('status');
    
    if (in_array($status, $statuses)) {
        $validatedEntities[] = $entity;
    } else {
        echo "✗ Entity " . $entity['id'] . " has invalid status: " . $status . "\n";
    }
}

echo "Valid entities: " . count($validatedEntities) . "\n";

// Example 6: Batch validation
print_section('6. Batch Validation');

$users = [
    ['id' => 1, 'name' => 'Alice', 'age' => 25],
    ['id' => 2, 'name' => 'Bob', 'age' => 17],  // Too young
    ['id' => 3, 'name' => 'Charlie', 'age' => 30],
    ['id' => 4, 'name' => '', 'age' => 28],  // No name
];

$validUsers = [];
$invalidUsers = [];

foreach ($users as $user) {
    $errors = [];
    
    if (empty($user['name'])) {
        $errors[] = 'Name is required';
    }
    
    if ($user['age'] < 18) {
        $errors[] = 'Must be at least 18';
    }
    
    if (empty($errors)) {
        $validUsers[] = $user;
    } else {
        $invalidUsers[] = [
            'user' => $user,
            'errors' => $errors,
        ];
    }
}

echo "Valid users: " . count($validUsers) . "\n";
echo "Invalid users: " . count($invalidUsers) . "\n";

foreach ($invalidUsers as $item) {
    echo "  User " . $item['user']['id'] . ": " . implode(', ', $item['errors']) . "\n";
}

// Example 7: Conditional validation
print_section('7. Conditional Validation');

$subscription = Json::create([
    'plan' => 'premium',
    'auto_renew' => true,
    'payment_method' => 'credit_card',
    'billing_day' => 15,
]);

$errors = [];

// Payment method validation only if auto_renew is true
if ($subscription->get('auto_renew')) {
    if (!$subscription->has('payment_method')) {
        $errors[] = 'Payment method is required for auto-renewal';
    }
    
    if ($subscription->get('billing_day') < 1 || $subscription->get('billing_day') > 28) {
        $errors[] = 'Billing day must be between 1 and 28';
    }
}

echo "Subscription validation:\n";
if (empty($errors)) {
    echo "✓ Valid subscription\n";
} else {
    echo "✗ Errors:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}

// Example 8: Custom validation rules
print_section('8. Custom Validation Rules');

$apiKey = Json::create([
    'key' => 'sk_live_abc123xyz',
    'name' => 'Production Key',
    'permissions' => ['read', 'write'],
    'expires_at' => '2025-12-31',
]);

$validationRules = [
    'key' => fn($val) => strlen($val) >= 10 && preg_match('/^[a-z0-9_]+$/', $val),
    'name' => fn($val) => strlen($val) >= 3,
    'permissions' => fn($val) => is_array($val) && !empty($val),
    'expires_at' => fn($val) => strtotime($val) > time(),
];

$errors = [];
foreach ($validationRules as $field => $rule) {
    if ($apiKey->has($field)) {
        if (!$rule($apiKey->get($field))) {
            $errors[] = "Invalid value for field: $field";
        }
    }
}

echo "API Key validation:\n";
if (empty($errors)) {
    echo "✓ Valid API key\n";
} else {
    echo "✗ Errors:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}

// Example 9: Response validation
print_section('9. Response Validation');

$response = Json::create([
    'success' => true,
    'status_code' => 200,
    'message' => 'OK',
    'data' => ['id' => 1, 'created_at' => date('c')],
    'timestamp' => date('c'),
]);

function validateApiResponse($response) {
    $errors = [];
    
    // Required fields
    if (!$response->has('success')) {
        $errors[] = 'success field is required';
    }
    
    if (!$response->has('status_code')) {
        $errors[] = 'status_code field is required';
    }
    
    // Type validation
    if ($response->has('success') && !is_bool($response->get('success'))) {
        $errors[] = 'success must be boolean';
    }
    
    if ($response->has('status_code') && !is_int($response->get('status_code'))) {
        $errors[] = 'status_code must be integer';
    }
    
    // Status code range
    if ($response->has('status_code')) {
        $code = $response->get('status_code');
        if ($code < 200 || $code >= 600) {
            $errors[] = 'status_code must be between 200 and 599';
        }
    }
    
    return $errors;
}

$errors = validateApiResponse($response);
echo "Response validation:\n";
if (empty($errors)) {
    echo "✓ Response is valid\n";
} else {
    echo "✗ Errors:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}

// Example 10: Validation report
print_section('10. Validation Report');

$bulkData = [
    ['id' => 1, 'email' => 'valid@example.com', 'age' => 25],
    ['id' => 2, 'email' => 'invalid', 'age' => 30],
    ['id' => 3, 'email' => '', 'age' => 17],
];

$report = [
    'total' => count($bulkData),
    'valid' => 0,
    'invalid' => 0,
    'errors' => [],
];

foreach ($bulkData as $index => $item) {
    $errors = [];
    
    if (empty($item['email'])) {
        $errors[] = 'Email is required';
    } elseif (!validateEmail($item['email'])) {
        $errors[] = 'Email format is invalid';
    }
    
    if ($item['age'] < 18) {
        $errors[] = 'Must be at least 18';
    }
    
    if (empty($errors)) {
        $report['valid']++;
    } else {
        $report['invalid']++;
        $report['errors'][] = [
            'index' => $index,
            'id' => $item['id'],
            'errors' => $errors,
        ];
    }
}

echo "Validation Report:\n";
echo "Total items: " . $report['total'] . "\n";
echo "Valid: " . $report['valid'] . "\n";
echo "Invalid: " . $report['invalid'] . "\n";

if (!empty($report['errors'])) {
    echo "\nErrors:\n";
    foreach ($report['errors'] as $error) {
        echo "  Item " . $error['index'] . " (ID: " . $error['id'] . "): " . implode(', ', $error['errors']) . "\n";
    }
}

print_section('Examples Complete');
