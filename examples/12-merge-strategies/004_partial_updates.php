<?php

/**
 * Partial Updates with Merge
 *
 * Update only specific fields.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;
use Skpassegna\Json\Enums\DiffMergeStrategy;

print_section('Partial Updates');

$user = Json::create([
    'id' => 1,
    'name' => 'Alice',
    'email' => 'alice@example.com',
    'age' => 28,
]);

$updates = ['email' => 'alice.new@example.com'];

$user->mergeWithStrategy($updates, DiffMergeStrategy::MERGE_SHALLOW);

echo "Updated user:\n";
echo "  Name: " . $user->get('name') . " (unchanged)\n";
echo "  Email: " . $user->get('email') . " (updated)\n";
echo "  Age: " . $user->get('age') . " (unchanged)\n";

print_section('Examples Complete');
