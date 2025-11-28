<?php

/**
 * Path Normalization
 *
 * Working with different path formats.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

print_section('Path Normalization');

$json = Json::create([
    'user' => ['name' => 'Alice', 'email' => 'alice@example.com'],
]);

// Both should work
$name1 = $json->get('user.name');
$name2 = $json->getPointer('/user/name');

echo "Dot notation: $name1\n";
echo "Pointer notation: $name2\n";
echo "Results match: " . ($name1 === $name2 ? 'yes' : 'no') . "\n";

print_section('Examples Complete');
