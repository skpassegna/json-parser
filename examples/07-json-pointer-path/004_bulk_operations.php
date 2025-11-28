<?php

/**
 * Bulk Operations with Pointers and Paths
 *
 * Batch operations using pointer and path syntax.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example: Bulk set operations
print_section('Bulk Pointer Operations');

$json = Json::create();

$pointers = [
    '/settings/theme' => 'dark',
    '/settings/language' => 'en',
    '/settings/notifications' => true,
    '/user/name' => 'Alice',
    '/user/email' => 'alice@example.com',
];

foreach ($pointers as $pointer => $value) {
    $json->setPointer($pointer, $value);
}

print_json($json);

print_section('Examples Complete');
