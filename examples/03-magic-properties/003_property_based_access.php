<?php

/**
 * Property-Based Access Patterns
 *
 * Demonstrates object property patterns and conversions.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example: Property-based access
print_section('Property-Based Access');

$data = Json::create([
    'user' => ['name' => 'Alice', 'age' => 28],
    'settings' => ['theme' => 'dark'],
]);

$obj = (object)$data->toArray();

echo "Name: " . $obj->user['name'] . "\n";
echo "Theme: " . $obj->settings['theme'] . "\n";

print_section('Examples Complete');
