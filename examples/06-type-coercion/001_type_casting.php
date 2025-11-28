<?php

/**
 * Type Coercion and Casting
 *
 * Demonstrates converting between different data types and using
 * the type coercion service for safe conversions.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Convert to array
print_section('1. Convert to Array');
$json = Json::create([
    'id' => 1,
    'name' => 'Test',
    'active' => true,
    'data' => ['key' => 'value'],
]);

$array = $json->toArray();
echo "Type: " . gettype($array) . "\n";
echo "Is array: " . (is_array($array) ? 'yes' : 'no') . "\n";
echo "Keys: " . implode(', ', array_keys($array)) . "\n";

// Example 2: Convert to string (JSON)
print_section('2. Convert to JSON String');
$json = Json::create([
    'user' => ['name' => 'John', 'age' => 30],
    'active' => true,
]);

$jsonString = $json->toString();
echo "Type: " . gettype($jsonString) . "\n";
echo "Length: " . strlen($jsonString) . " bytes\n";
echo "Content: " . $jsonString . "\n";

// Example 3: Convert to object
print_section('3. Convert to Object (stdClass)');
$json = Json::create([
    'name' => 'Product',
    'price' => 99.99,
    'in_stock' => true,
]);

$obj = (object)$json->toArray();
echo "Type: " . get_class($obj) . "\n";
echo "Properties: " . implode(', ', array_keys((array)$obj)) . "\n";
echo "Access via property: " . $obj->name . "\n";

// Example 4: String casting with __toString
print_section('4. Use __toString Magic Method');
$json = Json::create(['message' => 'Hello World']);

// Direct string usage
echo "Direct echo: " . $json . "\n";

// Concatenation
$text = "JSON: " . $json;
echo "Concatenation: " . $text . "\n";

// Example 5: Type coercion in comparisons
print_section('5. Type Coercion in Comparisons');
$json = Json::create(['value' => '123']);

$value = $json->get('value');
echo "String value: '$value'\n";
echo "Type: " . gettype($value) . "\n";

// Safe integer conversion
if (is_numeric($value)) {
    $intValue = (int)$value;
    echo "As integer: $intValue\n";
    echo "Type: " . gettype($intValue) . "\n";
}

// Example 6: Boolean conversion
print_section('6. Boolean Type Handling');
$json = Json::create([
    'active' => true,
    'deleted' => false,
    'verified' => 1,
    'enabled' => 0,
]);

foreach ($json->toArray() as $key => $value) {
    $bool = (bool)$value;
    echo "$key: " . var_export($value, true) . " -> bool: " . ($bool ? 'true' : 'false') . "\n";
}

// Example 7: Numeric type conversion
print_section('7. Numeric Conversions');
$json = Json::create([
    'int_val' => 42,
    'float_val' => 3.14159,
    'string_int' => '100',
    'string_float' => '99.99',
]);

$data = $json->toArray();
echo "Integer: " . $data['int_val'] . " (type: " . gettype($data['int_val']) . ")\n";
echo "Float: " . $data['float_val'] . " (type: " . gettype($data['float_val']) . ")\n";
echo "String int: " . $data['string_int'] . " (type: " . gettype($data['string_int']) . ")\n";
echo "  As int: " . ((int)$data['string_int']) . "\n";
echo "String float: " . $data['string_float'] . " (type: " . gettype($data['string_float']) . ")\n";
echo "  As float: " . ((float)$data['string_float']) . "\n";

// Example 8: Array/string conversion
print_section('8. Array to String and Back');
$original = Json::create(['items' => [1, 2, 3, 4, 5]]);

// To string
$jsonString = (string)$original;
echo "JSON string: " . substr($jsonString, 0, 50) . "...\n";

// Back from string
$restored = Json::parse($jsonString);
echo "Restored items: " . implode(', ', $restored->get('items')) . "\n";

// Example 9: Null handling
print_section('9. Null Type Handling');
$json = Json::create([
    'defined' => 'value',
    'empty_string' => '',
    'zero' => 0,
    'null_value' => null,
    'false_value' => false,
]);

$data = $json->toArray();
foreach ($data as $key => $value) {
    echo "$key: ";
    echo "is_null=" . (is_null($value) ? 'yes' : 'no') . ", ";
    echo "empty=" . (empty($value) ? 'yes' : 'no') . ", ";
    echo "type=" . gettype($value) . "\n";
}

// Example 10: Practical type coercion
print_section('10. Practical Type Coercion');
$rawData = Json::create([
    'user_id' => '12345',
    'amount' => '99.99',
    'active' => 'true',
    'count' => '42.5',
]);

// Coerce to appropriate types
$userId = (int)$rawData->get('user_id');
$amount = (float)$rawData->get('amount');
$active = $rawData->get('active') === 'true' || $rawData->get('active') === '1';
$count = (int)$rawData->get('count');

echo "User ID (int): $userId\n";
echo "Amount (float): " . number_format($amount, 2) . "\n";
echo "Active (bool): " . ($active ? 'yes' : 'no') . "\n";
echo "Count (int): $count\n";

print_section('Examples Complete');
