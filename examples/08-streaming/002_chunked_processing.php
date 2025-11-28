<?php

/**
 * Chunked Processing
 *
 * Demonstrates processing data in chunks for memory efficiency.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Process in chunks
print_section('1. Process Data in Chunks');

$largeData = [];
for ($i = 0; $i < 1000; $i++) {
    $largeData[] = ['id' => $i, 'value' => random_int(1, 100)];
}

$chunkSize = 100;
$chunks = array_chunk($largeData, $chunkSize);

$totalValue = 0;
foreach ($chunks as $index => $chunk) {
    $chunkTotal = array_sum(array_column($chunk, 'value'));
    $totalValue += $chunkTotal;
    echo "Chunk " . ($index + 1) . ": " . count($chunk) . " items, value = $chunkTotal\n";
}

echo "Total value: $totalValue\n";

// Example 2: Batch processing
print_section('2. Batch Processing with Callbacks');

function processBatch($items, $callback) {
    $batchSize = 10;
    $batches = array_chunk($items, $batchSize);
    
    foreach ($batches as $batch) {
        $callback($batch);
    }
}

$results = [];
processBatch($largeData, function($batch) use (&$results) {
    $sum = array_sum(array_column($batch, 'value'));
    $results[] = ['batch_sum' => $sum, 'count' => count($batch)];
});

echo "Processed " . count($results) . " batches\n";

print_section('Examples Complete');
