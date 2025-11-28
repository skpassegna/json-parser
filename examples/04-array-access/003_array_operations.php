<?php

/**
 * Array Operations with ArrayAccess
 *
 * Using standard array operations with Json objects.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example: Array operations
print_section('Array Operations');

$json = Json::create([
    'item1' => 'value1',
    'item2' => 'value2',
]);

echo "Items in Json:\n";
foreach ($json as $key => $value) {
    echo "  [$key] = $value\n";
}

echo "\nTotal items: " . count($json) . "\n";

print_section('Examples Complete');
