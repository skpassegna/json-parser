<?php

/**
 * Merge and Diff Strategies
 *
 * Demonstrates different merge and diff strategies for combining and comparing
 * JSON documents using RFC-compliant approaches.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;
use Skpassegna\Json\Enums\DiffMergeStrategy;

// Example 1: Simple merge
print_section('1. Simple Merge');
$json1 = Json::create([
    'name' => 'John',
    'age' => 30,
    'city' => 'NYC',
]);

$json2 = [
    'age' => 31,  // Override
    'country' => 'USA',  // New field
];

echo "Original:\n";
print_json($json1);

$json1->mergeWithStrategy($json2, DiffMergeStrategy::MERGE_SHALLOW);
echo "\nAfter shallow merge:\n";
print_json($json1);

// Example 2: Deep merge
print_section('2. Deep Merge (Recursive)');
$json1 = Json::create([
    'user' => [
        'name' => 'Alice',
        'email' => 'alice@example.com',
        'profile' => [
            'bio' => 'Developer',
        ],
    ],
]);

$json2 = [
    'user' => [
        'profile' => [
            'location' => 'NYC',
        ],
    ],
];

echo "Original:\n";
print_json($json1);

$json1->mergeWithStrategy($json2, DiffMergeStrategy::MERGE_RECURSIVE);
echo "\nAfter deep merge:\n";
print_json($json1);

// Example 3: Replace merge strategy
print_section('3. Replace Merge Strategy');
$json1 = Json::create([
    'config' => [
        'debug' => true,
        'timeout' => 30,
        'retries' => 3,
    ],
]);

$json2 = [
    'config' => [
        'debug' => false,
    ],
];

echo "Original:\n";
print_json($json1);

$json1->mergeWithStrategy($json2, DiffMergeStrategy::MERGE_REPLACE);
echo "\nAfter replace merge:\n";
print_json($json1);

// Example 4: Merge arrays
print_section('4. Merge with Array Handling');
$json1 = Json::create([
    'tags' => ['php', 'json'],
    'permissions' => ['read', 'write'],
]);

$json2 = [
    'tags' => ['api', 'rest'],
    'permissions' => ['admin'],
];

echo "Original:\n";
print_json($json1);

// Shallow merge replaces arrays
$json1->mergeWithStrategy($json2, DiffMergeStrategy::MERGE_SHALLOW);
echo "\nAfter merge (arrays replaced):\n";
print_json($json1);

// Example 5: RFC 7396 JSON Merge Patch
print_section('5. RFC 7396 JSON Merge Patch');
$json1 = Json::create([
    'name' => 'John',
    'age' => 30,
    'email' => 'john@example.com',
]);

$patch = [
    'age' => 31,
    'phone' => '+1-555-0123',
    'email' => null,  // Remove with null
];

echo "Original:\n";
print_json($json1);

$json1->mergeWithStrategy($patch, DiffMergeStrategy::MERGE_PATCH_RFC7396);
echo "\nAfter RFC7396 patch:\n";
print_json($json1);

// Example 6: Diff operations
print_section('6. Diff Between Documents');
$original = Json::create([
    'id' => 1,
    'name' => 'Product A',
    'price' => 99.99,
    'stock' => 50,
]);

$updated = [
    'id' => 1,
    'name' => 'Product A',
    'price' => 89.99,  // Changed
    'stock' => 45,     // Changed
    'category' => 'Electronics',  // New
];

echo "Original:\n";
print_json($original);

echo "\nUpdated:\n";
print_json($updated);

// Perform diff
$diff = $original->diffWithStrategy($updated, DiffMergeStrategy::DIFF_DETAILED);
echo "\nDetailed Diff:\n";
var_export($diff);

// Example 7: Shallow merge
print_section('7. Shallow Merge');
$json1 = Json::create([
    'user' => [
        'id' => 1,
        'name' => 'Alice',
    ],
    'meta' => ['updated' => '2025-01-01'],
]);

$json2 = [
    'user' => [
        'name' => 'Alice Updated',
        'email' => 'alice@example.com',
    ],
];

echo "Before shallow merge:\n";
print_json($json1);

$json1->mergeWithStrategy($json2, DiffMergeStrategy::MERGE_SHALLOW);
echo "\nAfter shallow merge (user object replaced):\n";
print_json($json1);

// Example 8: Conflict-aware merge
print_section('8. Conflict-Aware Merge');
$json1 = Json::create([
    'version' => 1,
    'data' => ['field1' => 'value1'],
]);

$json2 = [
    'version' => 2,
    'data' => ['field1' => 'modified_value1'],
];

echo "Document 1:\n";
print_json($json1);

echo "\nDocument 2:\n";
print_json($json2);

try {
    $json1->mergeWithStrategy($json2, DiffMergeStrategy::MERGE_CONFLICT_AWARE);
    echo "\nMerge result:\n";
    print_json($json1);
} catch (Exception $e) {
    echo "Conflict detected: " . $e->getMessage() . "\n";
}

// Example 9: Practical - Config merging
print_section('9. Practical: Configuration Merging');
$defaultConfig = Json::create([
    'app' => [
        'name' => 'MyApp',
        'version' => '1.0',
        'debug' => false,
    ],
    'database' => [
        'host' => 'localhost',
        'port' => 5432,
    ],
]);

$envConfig = [
    'app' => [
        'debug' => true,
    ],
    'database' => [
        'host' => 'prod.db.example.com',
    ],
];

echo "Default Config:\n";
print_json($defaultConfig);

$finalConfig = Json::create($defaultConfig->toArray());
$finalConfig->mergeWithStrategy($envConfig, DiffMergeStrategy::MERGE_RECURSIVE);

echo "\nMerged Config:\n";
print_json($finalConfig);

// Example 10: Practical - API version migration
print_section('10. Practical: API Response Evolution');
$v1Response = Json::create([
    'id' => 1,
    'user' => 'John',
    'timestamp' => '2025-01-01T00:00:00Z',
]);

// v2 adds new fields
$v2Updates = [
    'user' => [
        'name' => 'John Doe',
        'id' => 1,
    ],
    'created_at' => '2025-01-01T00:00:00Z',
    'metadata' => ['version' => 2],
];

echo "V1 Response:\n";
print_json($v1Response);

$v1Response->mergeWithStrategy($v2Updates, DiffMergeStrategy::MERGE_RECURSIVE);

echo "\nUpgraded Response:\n";
print_json($v1Response);

print_section('Examples Complete');
