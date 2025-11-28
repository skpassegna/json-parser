<?php

/**
 * Diff Operations and Change Detection
 *
 * Demonstrates detecting and tracking changes between documents.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;
use Skpassegna\Json\Enums\DiffMergeStrategy;

// Example 1: Detect changes
print_section('1. Detect Changes Between Documents');

$original = Json::create([
    'id' => 1,
    'name' => 'Product A',
    'price' => 100,
    'stock' => 50,
]);

$updated = [
    'id' => 1,
    'name' => 'Product A Updated',  // Changed
    'price' => 100,
    'stock' => 45,  // Changed
    'discount' => 10,  // Added
];

$changes = [];

foreach ($updated as $key => $newValue) {
    $oldValue = $original->get($key);
    
    if ($oldValue !== $newValue) {
        $changes[] = [
            'field' => $key,
            'old' => $oldValue,
            'new' => $newValue,
        ];
    }
}

// Check for removed fields
foreach ($original->toArray() as $key => $value) {
    if (!isset($updated[$key])) {
        $changes[] = [
            'field' => $key,
            'old' => $value,
            'new' => null,
        ];
    }
}

echo "Changes detected:\n";
foreach ($changes as $change) {
    echo "  - " . $change['field'] . ": ";
    echo var_export($change['old'], true) . " -> ";
    echo var_export($change['new'], true) . "\n";
}

// Example 2: Create changelog
print_section('2. Generate Changelog');

$versions = [
    '1.0' => ['name' => 'Initial', 'version' => '1.0'],
    '1.1' => ['name' => 'Initial', 'version' => '1.1', 'feature' => 'new_feature'],
    '2.0' => ['name' => 'Complete Rewrite', 'version' => '2.0'],
];

$changelog = [];
$previous = null;

foreach ($versions as $version => $current) {
    if ($previous !== null) {
        $entry = [
            'version' => $version,
            'changes' => [],
        ];
        
        foreach ($current as $key => $value) {
            if (!isset($previous[$key]) || $previous[$key] !== $value) {
                $entry['changes'][] = "$key changed";
            }
        }
        
        if (!empty($entry['changes'])) {
            $changelog[] = $entry;
        }
    }
    
    $previous = $current;
}

echo "Changelog:\n";
foreach ($changelog as $entry) {
    echo "  v" . $entry['version'] . ":\n";
    foreach ($entry['changes'] as $change) {
        echo "    - $change\n";
    }
}

print_section('Examples Complete');
