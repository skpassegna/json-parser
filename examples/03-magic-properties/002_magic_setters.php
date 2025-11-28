<?php

/**
 * Magic Property Setters
 *
 * While Json uses get/set methods, demonstrates property manipulation patterns
 * and array-to-object conversions for property access workflows.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Work with object properties
print_section('1. Convert to Object and Modify');

$json = Json::create([
    'id' => 1,
    'name' => 'Product',
    'price' => 99.99,
]);

// Convert to object for property access
$obj = (object)$json->toArray();
$obj->name = 'Updated Product';
$obj->discount = 10;

// Convert back to Json
$updated = Json::create((array)$obj);
print_json($updated);

// Example 2: Dynamic property access
print_section('2. Dynamic Property Creation');

$data = Json::create();

// Simulate dynamic property setting
$properties = [
    'field1' => 'value1',
    'field2' => 'value2',
    'nested.field' => 'nested_value',
];

foreach ($properties as $path => $value) {
    $data->set($path, $value);
}

print_json($data);

// Example 3: Merge object properties
print_section('3. Merge Object into Json');

$baseJson = Json::create(['id' => 1, 'name' => 'Base']);

$updates = (object)[
    'name' => 'Updated',
    'email' => 'test@example.com',
];

$baseJson->mergeWithStrategy((array)$updates, \Skpassegna\Json\Enums\DiffMergeStrategy::MERGE_SHALLOW);
print_json($baseJson);

print_section('Examples Complete');
