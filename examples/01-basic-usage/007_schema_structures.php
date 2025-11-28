<?php

/**
 * Working with Schema Structures
 *
 * Creating and validating against schemas.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

print_section('Schema-like Structures');

// Define a schema-like structure
$schema = [
    'name' => 'string',
    'age' => 'int',
    'email' => 'string',
    'active' => 'bool',
];

$data = Json::create([
    'name' => 'Alice',
    'age' => 28,
    'email' => 'alice@example.com',
    'active' => true,
]);

echo "Data structure:\n";
foreach ($schema as $field => $type) {
    $value = $data->get($field);
    echo "  $field ($type): ";
    echo var_export($value, true) . " [" . gettype($value) . "]\n";
}

print_section('Examples Complete');
