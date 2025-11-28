<?php

declare(strict_types=1);

/**
 * Security Best Practices - Input Validation
 *
 * This example demonstrates security best practices when working with JSON,
 * including input validation, depth limits, and sanitization.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Skpassegna\Json\Json;
use Skpassegna\Json\Exceptions\ParseException;

echo "=== Security - Input Validation ===\n\n";

// Example 1: Depth limit protection
echo "1. Depth limit protection (prevent deeply nested JSON attacks):\n";
$deepJson = '{"a":{"b":{"c":{"d":{"e":{"f":{"g":"value"}}}}}}}';

echo "   Parsing with depth limit = 3:\n";
try {
    $json = Json::parse($deepJson, ['max_depth' => 3]);
    echo "   ✓ Parsed successfully (depth within limit)\n";
} catch (ParseException $e) {
    echo "   ✗ Parse failed: " . $e->getMessage() . "\n";
}

echo "   Parsing with depth limit = 2:\n";
try {
    $json = Json::parse($deepJson, ['max_depth' => 2]);
    echo "   ✓ Parsed successfully\n";
} catch (ParseException $e) {
    echo "   ✗ Parse failed (depth exceeded): Caught with max_depth protection\n";
}
echo "\n";

// Example 2: Length limit protection
echo "2. Length limit protection (prevent DoS attacks):\n";
$largeJson = json_encode(array_fill(0, 1000, 'value'));

echo "   JSON size: " . strlen($largeJson) . " bytes\n";
echo "   Parsing with max_length = 500 bytes:\n";
try {
    $json = Json::parse($largeJson, ['max_length' => 500]);
    echo "   ✓ Parsed successfully\n";
} catch (ParseException $e) {
    echo "   ✗ Parse failed (size exceeded): Caught with max_length protection\n";
}

echo "   Parsing with max_length = 10000 bytes:\n";
try {
    $json = Json::parse($largeJson, ['max_length' => 10000]);
    echo "   ✓ Parsed successfully (within limit)\n";
} catch (ParseException $e) {
    echo "   ✗ Parse failed: " . $e->getMessage() . "\n";
}
echo "\n";

// Example 3: Invalid JSON protection
echo "3. Invalid JSON protection:\n";
$invalidJsonExamples = [
    'incomplete' => '{"name": "Alice"',
    'trailing_comma' => '{"name": "Alice",}',
    'single_quotes' => "{'name': 'Alice'}",
    'unquoted_keys' => '{name: "Alice"}',
];

foreach ($invalidJsonExamples as $type => $invalidJson) {
    echo "   Testing $type:\n";
    try {
        $json = Json::parse($invalidJson);
        echo "     ✓ Parsed (unexpected)\n";
    } catch (ParseException $e) {
        echo "     ✗ Safely rejected: Malformed JSON detected\n";
    }
}
echo "\n";

// Example 4: Sanitization
echo "4. Input sanitization:\n";
$jsonWithSpecialChars = json_encode([
    'html' => '<script>alert("XSS")</script>',
    'sql' => "'; DROP TABLE users; --",
    'normal' => 'clean text',
]);

echo "   Original JSON: " . $jsonWithSpecialChars . "\n";
$json = Json::parse($jsonWithSpecialChars, ['sanitize' => true]);
echo "   Sanitized: " . $json->toString() . "\n\n";

// Example 5: Safe defaults
echo "5. Safe defaults and error handling:\n";
$json = Json::parse('{"name": "Alice", "age": 30}');

// Always use default values for missing paths
$phone = $json->get('phone', 'N/A');
echo "   Missing 'phone' with default: " . $phone . "\n";

// Always validate before accessing
if ($json->has('email')) {
    $email = $json->get('email');
} else {
    $email = 'not provided';
}
echo "   Safe access to optional 'email': " . $email . "\n\n";

// Example 6: Schema validation
echo "6. Schema validation before processing:\n";
$schema = [
    'type' => 'object',
    'properties' => [
        'name' => ['type' => 'string'],
        'age' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 150],
    ],
    'required' => ['name'],
];

$validData = '{"name": "Alice", "age": 30}';
$invalidData = '{"name": "Alice", "age": -5}'; // Age is negative

echo "   Validating valid data:\n";
$jsonValid = Json::parse($validData);
echo "     Valid: " . ($jsonValid->validateSchema($schema) ? 'YES' : 'NO') . "\n";

echo "   Validating invalid data:\n";
$jsonInvalid = Json::parse($invalidData);
echo "     Valid: " . ($jsonInvalid->validateSchema($schema) ? 'YES' : 'NO') . "\n\n";

echo "Security validation examples completed!\n";
