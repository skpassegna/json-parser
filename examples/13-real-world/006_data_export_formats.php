<?php

/**
 * Real-World: Data Export Formats
 *
 * Converting and exporting JSON to various formats.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example: Export formats
print_section('Data Export Formats');

$data = Json::create([
    'id' => 1,
    'name' => 'Product',
    'price' => 99.99,
]);

echo "Compact JSON:\n";
echo $data->toString(0) . "\n\n";

echo "Pretty JSON:\n";
echo $data->toString(JSON_PRETTY_PRINT) . "\n\n";

echo "Unescaped:\n";
echo $data->toString(JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n";

print_section('Examples Complete');
