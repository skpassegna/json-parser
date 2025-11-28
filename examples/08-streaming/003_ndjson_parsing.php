<?php

/**
 * NDJSON Parsing and Processing
 *
 * Working with newline-delimited JSON format.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example: Parse NDJSON
print_section('NDJSON Processing');

$file = get_example_file('items.ndjson');

if (file_exists($file)) {
    $lines = file($file, FILE_IGNORE_NEW_LINES);
    
    echo "Total items: " . count($lines) . "\n";
    echo "Sample items:\n";
    
    $count = 0;
    foreach ($lines as $line) {
        if (trim($line)) {
            $json = Json::parse($line);
            echo "  - " . $json->get('name') . " (\$" . $json->get('price') . ")\n";
            
            if (++$count >= 3) break;
        }
    }
}

print_section('Examples Complete');
