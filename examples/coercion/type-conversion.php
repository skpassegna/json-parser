<?php

declare(strict_types=1);

/**
 * Type Coercion and Normalization
 *
 * This example demonstrates how to use the type coercion service
 * to convert and normalize values to specific types.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Skpassegna\Json\Json;

echo "=== Type Coercion Examples ===\n\n";

// Example 1: Strict vs Lenient coercion
echo "1. Strict vs Lenient coercion:\n";
$json = Json::parse('{"value": "42"}');

// Lenient mode (default)
$json->enableStrictCoercion(false);
echo "   Lenient mode:\n";
echo "     '42' to int: " . $json->coerceInt('42') . "\n";
echo "     'true' to bool: " . ($json->coerceBool('true') ? 'true' : 'false') . "\n";
echo "     '3.14' to float: " . $json->coerceFloat('3.14') . "\n\n";

// Strict mode
$json->enableStrictCoercion(true);
echo "   Strict mode:\n";
try {
    echo "     '42' to int: " . $json->coerceInt('42') . "\n";
    echo "     'true' to bool: " . ($json->coerceBool('true') ? 'true' : 'false') . "\n";
} catch (\Exception $e) {
    echo "     Strict coercion may throw exceptions for non-standard formats\n";
}
echo "\n";

// Example 2: Array coercion
echo "2. Array coercion:\n";
$json->enableStrictCoercion(false);
$result = $json->coerceArrayType('not an array');
echo "   'not an array' to array: " . json_encode($result) . "\n";
$result = $json->coerceArrayType(['key' => 'value']);
echo "   ['key' => 'value'] to array: " . json_encode($result) . "\n\n";

// Example 3: Object coercion
echo "3. Object coercion:\n";
$arrayData = ['name' => 'Alice', 'age' => 30];
$object = $json->coerceObject($arrayData);
echo "   Array to object: " . json_encode($object) . "\n\n";

// Example 4: String coercion
echo "4. String coercion:\n";
echo "   123 to string: '" . $json->coerceString(123) . "'\n";
echo "   true to string: '" . $json->coerceString(true) . "'\n";
echo "   null to string: '" . ($json->coerceString(null) ?? 'null') . "'\n";
echo "   Array to string: '" . $json->coerceString(['a', 'b']) . "'\n\n";

// Example 5: Null coercion and handling empty values
echo "5. Null coercion and empty values:\n";
echo "   '' (empty string) to null: " . ($json->coerceNull('') === null ? 'null' : 'not null') . "\n";
echo "   0 to null: " . ($json->coerceNull(0) === null ? 'null' : 'not null') . "\n";
echo "   'null' string to null: " . ($json->coerceNull('null') === null ? 'null' : 'not null') . "\n";
echo "   false to null: " . ($json->coerceNull(false) === null ? 'null' : 'not null') . "\n\n";

echo "All type coercion examples completed!\n";
