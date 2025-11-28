<?php

/**
 * Stream Aggregation
 *
 * Aggregate data while streaming.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

print_section('Stream Aggregation');

$file = get_example_file('items.ndjson');

if (file_exists($file)) {
    $total = 0;
    $count = 0;
    
    $lines = file($file, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        if (trim($line)) {
            $json = Json::parse($line);
            $total += $json->get('price');
            $count++;
        }
    }
    
    echo "Items: $count\n";
    echo "Total: \$" . number_format($total, 2) . "\n";
    echo "Average: \$" . number_format($total / $count, 2) . "\n";
}

print_section('Examples Complete');
