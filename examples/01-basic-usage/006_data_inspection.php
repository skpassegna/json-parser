<?php

/**
 * Data Inspection and Analysis
 *
 * Inspecting and analyzing JSON structure and content.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example: Inspect data
print_section('Data Inspection');

$json = Json::parse(file_get_contents(get_example_file('users.json')));

echo "Total keys: " . count($json->toArray()) . "\n";
echo "Has users: " . ($json->has('users') ? 'yes' : 'no') . "\n";
echo "User count: " . count($json->get('users')) . "\n";

print_section('Examples Complete');
