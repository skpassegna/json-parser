<?php

/**
 * Error Handling and Exceptions
 *
 * Demonstrates proper error handling and exception management when working
 * with JSON parsing and operations.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;
use Skpassegna\Json\Exceptions\ParseException;

// Example 1: Handle parse errors
print_section('1. Catch Parse Errors');

$invalidJson = '{"incomplete": json';

try {
    $json = Json::parse($invalidJson);
} catch (ParseException $e) {
    echo "Parse error caught: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Other exception: " . get_class($e) . "\n";
}

// Example 2: Validate before parsing
print_section('2. Validate Before Parsing');

$jsonStrings = [
    '{"valid": "json"}',
    '{"invalid": json}',
    '{"also": "valid"}',
];

foreach ($jsonStrings as $index => $jsonStr) {
    try {
        $json = Json::parse($jsonStr);
        echo ($index + 1) . ". Valid JSON parsed\n";
    } catch (ParseException $e) {
        echo ($index + 1) . ". Invalid JSON: " . $e->getMessage() . "\n";
    }
}

// Example 3: Safe get with defaults
print_section('3. Safe Get with Defaults');

$json = Json::create(['user' => ['name' => 'Alice']]);

// Missing nested path returns null
$missing = $json->get('user.missing.path');
echo "Missing path result: " . var_export($missing, true);

// Use default
$withDefault = $json->get('user.missing', 'default_value');
echo "With default: $withDefault\n";

// Example 4: Check existence before access
print_section('4. Check Existence Before Access');

$json = Json::create(['id' => 1, 'name' => 'Test']);

// Safe access pattern
if ($json->has('email')) {
    $email = $json->get('email');
} else {
    $email = 'not_provided';
}

echo "Email: $email\n";

// Example 5: Catch runtime errors
print_section('5. Handle Runtime Errors');

$json = Json::create(['data' => 'value']);

// Attempting invalid operations
try {
    // This might throw if invalid
    $json->set('field', fopen('php://memory', 'r'));
} catch (Exception $e) {
    echo "Operation error: " . $e->getMessage() . "\n";
}

print_section('Examples Complete');
