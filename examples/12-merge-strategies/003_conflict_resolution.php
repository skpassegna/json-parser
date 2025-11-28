<?php

/**
 * Conflict Resolution in Merges
 *
 * Handling conflicts during merge operations.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example: Handle merge conflicts
print_section('Merge Conflict Handling');

$doc1 = Json::create(['field' => 'value1', 'id' => 1]);
$doc2 = ['field' => 'value2', 'id' => 1];

echo "Document 1:\n";
print_json($doc1);

echo "\nDocument 2:\n";
print_json($doc2);

echo "\nAfter merge (doc2 wins):\n";
$doc1->mergeWithStrategy($doc2, \Skpassegna\Json\Enums\DiffMergeStrategy::MERGE_SHALLOW);
print_json($doc1);

print_section('Examples Complete');
