<?php

declare(strict_types=1);

/**
 * Basic Procedural API Usage
 *
 * This example demonstrates how to use the procedural API functions
 * for JSON manipulation without writing object-oriented code.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use function Skpassegna\Json\Procedural\{
    json_parse,
    json_create,
    json_get,
    json_set,
    json_has,
    json_remove,
    json_stringify,
    json_pretty,
};

echo "=== Procedural API - Basic Usage ===\n\n";

// Example 1: Parse JSON
echo "1. Parse JSON string:\n";
$jsonString = '{"name": "Alice", "age": 30, "email": "alice@example.com"}';
$user = json_parse($jsonString);
echo "   Parsed: " . json_stringify($user) . "\n\n";

// Example 2: Get values
echo "2. Get values from JSON:\n";
$name = json_get($user, 'name');
$age = json_get($user, 'age');
echo "   Name: $name\n";
echo "   Age: $age\n\n";

// Example 3: Set values
echo "3. Set values in JSON:\n";
json_set($user, 'age', 31);
json_set($user, 'city', 'New York');
echo "   Updated: " . json_pretty($user) . "\n\n";

// Example 4: Check if path exists
echo "4. Check if paths exist:\n";
echo "   Has 'name': " . (json_has($user, 'name') ? 'yes' : 'no') . "\n";
echo "   Has 'phone': " . (json_has($user, 'phone') ? 'yes' : 'no') . "\n\n";

// Example 5: Remove a value
echo "5. Remove a value:\n";
json_remove($user, 'email');
echo "   After removing 'email': " . json_pretty($user) . "\n\n";

// Example 6: Create new JSON from scratch
echo "6. Create new JSON from scratch:\n";
$newData = json_create();
json_set($newData, 'title', 'My Project');
json_set($newData, 'version', '1.0.0');
json_set($newData, 'author.name', 'John Doe');
json_set($newData, 'author.email', 'john@example.com');
echo "   Created: " . json_pretty($newData) . "\n\n";

// Example 7: Using array data directly (no need for Json instance)
echo "7. Get from plain array (converted internally):\n";
$plainArray = ['status' => 'success', 'code' => 200];
$status = json_get($plainArray, 'status');
echo "   Status: $status\n";
echo "   Code: " . json_get($plainArray, 'code') . "\n\n";

echo "All examples completed successfully!\n";
