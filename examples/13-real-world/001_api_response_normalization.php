<?php

/**
 * Real-World: API Response Normalization
 *
 * Demonstrates normalizing various API response formats into a consistent structure,
 * handling inconsistent data from multiple endpoints.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Normalize different user formats
print_section('1. Normalize User Response Formats');

// Format 1: Simple user response
$response1 = [
    'success' => true,
    'user' => [
        'id' => 1,
        'name' => 'Alice',
        'email' => 'alice@example.com',
    ],
];

// Format 2: Nested user response
$response2 = [
    'data' => [
        'user' => [
            'userId' => 2,
            'fullName' => 'Bob',
            'contact' => [
                'email' => 'bob@example.com',
            ],
        ],
    ],
    'status' => 'ok',
];

// Format 3: Array of users
$response3 = [
    'result' => [
        ['user_id' => 3, 'user_name' => 'Charlie', 'user_email' => 'charlie@example.com'],
    ],
];

function normalizeUserResponse($response) {
    $json = Json::create($response);
    
    // Extract user data from different locations
    $user = $json->get('user') ?? $json->get('data.user') ?? $json->get('result.0');
    
    if (!$user) {
        throw new Exception('User data not found');
    }
    
    // Normalize fields
    return [
        'id' => $user['id'] ?? $user['userId'] ?? $user['user_id'] ?? null,
        'name' => $user['name'] ?? $user['fullName'] ?? $user['user_name'] ?? null,
        'email' => $user['email'] ?? $user['contact']['email'] ?? $user['user_email'] ?? null,
    ];
}

echo "Response 1 normalized:\n";
var_export(normalizeUserResponse($response1));

echo "\nResponse 2 normalized:\n";
var_export(normalizeUserResponse($response2));

echo "\nResponse 3 normalized:\n";
var_export(normalizeUserResponse($response3));

// Example 2: Handle errors and success responses
print_section('2. Normalize Success/Error Responses');

$successResponse = [
    'success' => true,
    'data' => [
        'id' => 1,
        'message' => 'Operation completed',
    ],
];

$errorResponse = [
    'success' => false,
    'error' => [
        'code' => 'VALIDATION_ERROR',
        'message' => 'Invalid input',
        'details' => ['field' => 'email'],
    ],
];

$errorResponse2 = [
    'status' => 'error',
    'error_code' => 'NOT_FOUND',
    'error_msg' => 'Resource not found',
];

function normalizeApiResponse($response) {
    $json = Json::create($response);
    
    $success = $json->get('success') ?? ($json->get('status') !== 'error');
    
    if ($success) {
        return [
            'status' => 'success',
            'data' => $json->get('data'),
        ];
    }
    
    return [
        'status' => 'error',
        'code' => $json->get('error.code') ?? $json->get('error_code') ?? 'UNKNOWN',
        'message' => $json->get('error.message') ?? $json->get('error_msg') ?? 'Unknown error',
    ];
}

echo "Success response normalized:\n";
print_json(normalizeApiResponse($successResponse));

echo "\nError response 1 normalized:\n";
print_json(normalizeApiResponse($errorResponse));

echo "\nError response 2 normalized:\n";
print_json(normalizeApiResponse($errorResponse2));

// Example 3: Pagination normalization
print_section('3. Normalize Pagination');

$pagination1 = [
    'data' => [1, 2, 3],
    'page' => 1,
    'per_page' => 3,
    'total' => 10,
];

$pagination2 = [
    'items' => [1, 2, 3],
    'current_page' => 1,
    'limit' => 3,
    'total_count' => 10,
];

function normalizePagination($response) {
    $json = Json::create($response);
    
    $data = $json->get('data') ?? $json->get('items') ?? [];
    $page = $json->get('page') ?? $json->get('current_page') ?? 1;
    $perPage = $json->get('per_page') ?? $json->get('limit') ?? count($data);
    $total = $json->get('total') ?? $json->get('total_count') ?? 0;
    
    return [
        'data' => $data,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => ceil($total / $perPage),
        ],
    ];
}

echo "Pagination format 1:\n";
print_json(normalizePagination($pagination1));

echo "\nPagination format 2:\n";
print_json(normalizePagination($pagination2));

// Example 4: Normalize timestamp formats
print_section('4. Normalize Timestamps');

$event1 = [
    'id' => 1,
    'occurred_at' => '2025-05-28T10:30:00Z',
];

$event2 = [
    'id' => 2,
    'timestamp' => 1609459200,
];

$event3 = [
    'id' => 3,
    'created' => '2025-05-28 10:30:00',
];

function normalizeTimestamp($value) {
    if (is_numeric($value)) {
        return date('c', (int)$value);
    }
    
    if (is_string($value)) {
        $ts = strtotime($value);
        return date('c', $ts);
    }
    
    return $value;
}

function normalizeEvent($response) {
    $json = Json::create($response);
    
    $timestamp = $json->get('occurred_at') ?? $json->get('timestamp') ?? $json->get('created');
    
    return [
        'id' => $json->get('id'),
        'timestamp' => normalizeTimestamp($timestamp),
    ];
}

echo "Event 1:\n";
print_json(normalizeEvent($event1));

echo "\nEvent 2:\n";
print_json(normalizeEvent($event2));

echo "\nEvent 3:\n";
print_json(normalizeEvent($event3));

// Example 5: Normalize structured data
print_section('5. Normalize Product Data');

$product1 = [
    'product_id' => 1,
    'product_name' => 'Laptop',
    'product_price' => 999.99,
    'in_stock' => true,
];

$product2 = [
    'id' => 2,
    'name' => 'Mouse',
    'price' => ['amount' => 29.99, 'currency' => 'USD'],
    'available' => true,
];

function normalizeProduct($response) {
    $json = Json::create($response);
    
    $price = $json->get('product_price') ?? $json->get('price');
    if (is_array($price)) {
        $price = $price['amount'] ?? 0;
    }
    
    return [
        'id' => $json->get('product_id') ?? $json->get('id'),
        'name' => $json->get('product_name') ?? $json->get('name'),
        'price' => (float)$price,
        'available' => $json->get('in_stock') ?? $json->get('available') ?? false,
    ];
}

echo "Product 1:\n";
print_json(normalizeProduct($product1));

echo "\nProduct 2:\n";
print_json(normalizeProduct($product2));

// Example 6: Batch normalization
print_section('6. Batch Normalize Responses');

$responses = [
    ['success' => true, 'data' => ['id' => 1]],
    ['success' => true, 'data' => ['id' => 2]],
    ['success' => false, 'error' => ['message' => 'Failed']],
];

$normalized = [];
foreach ($responses as $response) {
    $normalized[] = normalizeApiResponse($response);
}

echo "Batch normalized:\n";
foreach ($normalized as $item) {
    echo "  - Status: " . $item['status'];
    if ($item['status'] === 'success') {
        echo " (data: " . json_encode($item['data']) . ")";
    }
    echo "\n";
}

print_section('Examples Complete');
