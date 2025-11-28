<?php

/**
 * Safe Type Conversions
 *
 * Demonstrates safe and defensive type conversion patterns.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Safe integer conversion
print_section('1. Safe Integer Conversion');

$data = Json::create([
    'valid_int' => 42,
    'string_int' => '100',
    'float_like' => 3.14,
    'invalid' => 'not a number',
]);

$values = $data->toArray();

foreach ($values as $key => $value) {
    if (is_numeric($value)) {
        $int = (int)$value;
        echo "$key: converted to $int\n";
    } else {
        echo "$key: not convertible\n";
    }
}

// Example 2: Safe string conversion
print_section('2. Safe String Conversion');

$items = [
    'string' => 'hello',
    'number' => 123,
    'bool_true' => true,
    'bool_false' => false,
    'null_val' => null,
];

foreach ($items as $key => $value) {
    $str = $value === null ? 'null' : (string)$value;
    echo "$key: \"$str\"\n";
}

// Example 3: Safe boolean conversion
print_section('3. Safe Boolean Conversion');

$values = [
    '1' => '1',
    '0' => '0',
    'true' => 'true',
    'false' => 'false',
    'yes' => 'yes',
    'no' => 'no',
    'empty' => '',
];

foreach ($values as $label => $value) {
    $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    echo "$label: " . var_export($bool, true) . "\n";
}

// Example 4: Type validation before conversion
print_section('4. Validate Before Conversion');

function safeCast($value, $targetType) {
    switch ($targetType) {
        case 'int':
            return is_numeric($value) ? (int)$value : null;
        case 'float':
            return is_numeric($value) ? (float)$value : null;
        case 'bool':
            return is_bool($value) ? $value : filter_var($value, FILTER_VALIDATE_BOOLEAN);
        case 'string':
            return (string)$value;
        case 'array':
            return is_array($value) ? $value : [$value];
        default:
            return $value;
    }
}

$test = Json::create(['value' => '42']);
$value = $test->get('value');

echo "Original: " . var_export($value, true);
echo "As int: " . var_export(safeCast($value, 'int'), true);
echo "As float: " . var_export(safeCast($value, 'float'), true);
echo "As bool: " . var_export(safeCast($value, 'bool'), true);

print_section('Examples Complete');
