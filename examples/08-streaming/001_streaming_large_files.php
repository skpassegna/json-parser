<?php

/**
 * Streaming Large JSON Files
 *
 * Demonstrates memory-efficient processing of large JSON files
 * using the streaming parser, avoiding loading entire files into memory.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Parse NDJSON (newline-delimited JSON)
print_section('1. Parse NDJSON Stream');
$ndjsonFile = get_example_file('items.ndjson');

if (file_exists($ndjsonFile)) {
    $items = [];
    $lines = file($ndjsonFile, FILE_IGNORE_NEW_LINES);
    
    echo "Processing NDJSON file: $ndjsonFile\n";
    echo "Lines: " . count($lines) . "\n\n";
    
    foreach ($lines as $line) {
        if (trim($line)) {
            $json = Json::parse($line);
            $items[] = $json->toArray();
        }
    }
    
    echo "Parsed items:\n";
    foreach ($items as $item) {
        echo "  - " . $item['name'] . " (\$" . $item['price'] . ")\n";
    }
}

// Example 2: Calculate statistics from stream
print_section('2. Calculate Statistics from Stream');
if (file_exists($ndjsonFile)) {
    $totalPrice = 0;
    $inStockCount = 0;
    $itemCount = 0;
    
    $lines = file($ndjsonFile, FILE_IGNORE_NEW_LINES);
    
    foreach ($lines as $line) {
        if (trim($line)) {
            $json = Json::parse($line);
            $data = $json->toArray();
            
            $totalPrice += $data['price'];
            if ($data['in_stock']) {
                $inStockCount++;
            }
            $itemCount++;
        }
    }
    
    echo "Total items: $itemCount\n";
    echo "In stock: $inStockCount\n";
    echo "Out of stock: " . ($itemCount - $inStockCount) . "\n";
    echo "Average price: \$" . number_format($totalPrice / $itemCount, 2) . "\n";
    echo "Total value: \$" . number_format($totalPrice, 2) . "\n";
}

// Example 3: Filter streaming data
print_section('3. Filter Stream - Only In-Stock Items');
if (file_exists($ndjsonFile)) {
    $inStockItems = [];
    $lines = file($ndjsonFile, FILE_IGNORE_NEW_LINES);
    
    foreach ($lines as $line) {
        if (trim($line)) {
            $json = Json::parse($line);
            $data = $json->toArray();
            
            if ($data['in_stock']) {
                $inStockItems[] = $data;
            }
        }
    }
    
    echo "In-stock items:\n";
    foreach ($inStockItems as $item) {
        echo "  - " . $item['name'] . " (\$" . $item['price'] . ")\n";
    }
}

// Example 4: Transform stream data
print_section('4. Transform Stream Data');
if (file_exists($ndjsonFile)) {
    $transformed = [];
    $lines = file($ndjsonFile, FILE_IGNORE_NEW_LINES);
    
    foreach ($lines as $line) {
        if (trim($line)) {
            $json = Json::parse($line);
            $data = $json->toArray();
            
            // Add computed field
            $data['status'] = $data['in_stock'] ? 'Available' : 'Out of Stock';
            $data['markup'] = $data['price'] * 1.5;
            
            $transformed[] = $data;
        }
    }
    
    echo "Transformed items:\n";
    foreach ($transformed as $item) {
        echo "  - " . $item['name'] . " (\$" . $item['price'] . ") - " . $item['status'] . "\n";
    }
}

// Example 5: Process large JSON array sequentially
print_section('5. Process Large Array Sequentially');
$largeArray = Json::create(['items' => []]);

// Simulate large dataset
for ($i = 1; $i <= 1000; $i++) {
    $largeArray->set("items.$i", [
        'id' => $i,
        'value' => random_int(100, 1000),
        'timestamp' => time(),
    ]);
}

// Process in chunks instead of all at once
$items = $largeArray->get('items');
$chunkSize = 100;
$chunks = array_chunk($items, $chunkSize);

echo "Total items: " . count($items) . "\n";
echo "Chunks: " . count($chunks) . "\n";
echo "First chunk items: " . count($chunks[0]) . "\n";
echo "Last chunk items: " . count(end($chunks)) . "\n";

// Example 6: Stream output to file
print_section('6. Stream Output to File');
$outputFile = sys_get_temp_dir() . '/stream_output_' . uniqid() . '.ndjson';

$handle = fopen($outputFile, 'w');

for ($i = 1; $i <= 5; $i++) {
    $json = Json::create([
        'id' => $i,
        'name' => "Item $i",
        'timestamp' => date('c'),
    ]);
    
    fwrite($handle, $json->toString() . "\n");
}

fclose($handle);

echo "Written to: $outputFile\n";
echo "File size: " . filesize($outputFile) . " bytes\n";

// Cleanup
@unlink($outputFile);

// Example 7: Stream processing with callbacks
print_section('7. Stream Processing with Callbacks');
if (file_exists($ndjsonFile)) {
    $stats = [
        'total' => 0,
        'high_price' => 0,
        'low_price' => 0,
    ];
    
    $lines = file($ndjsonFile, FILE_IGNORE_NEW_LINES);
    
    foreach ($lines as $line) {
        if (trim($line)) {
            $json = Json::parse($line);
            $price = $json->get('price');
            
            $stats['total']++;
            if ($price > 50) {
                $stats['high_price']++;
            } else {
                $stats['low_price']++;
            }
        }
    }
    
    echo "Total items: " . $stats['total'] . "\n";
    echo "High price (>50): " . $stats['high_price'] . "\n";
    echo "Low price (<=50): " . $stats['low_price'] . "\n";
}

// Example 8: Memory-efficient large data handling
print_section('8. Memory Efficient Processing');
// Rather than loading everything, process incrementally
$processed = 0;
$total = 0;

if (file_exists($ndjsonFile)) {
    $lines = file($ndjsonFile, FILE_IGNORE_NEW_LINES);
    
    foreach ($lines as $line) {
        if (trim($line)) {
            $total++;
            
            $json = Json::parse($line);
            // Do processing
            
            $processed++;
            
            if ($processed % 1 === 0) {
                echo "Processed: $processed/$total\r";
            }
        }
    }
}

echo "\nTotal processed: $processed\n";

// Example 9: Concatenate multiple streams
print_section('9. Concatenate Multiple JSON Objects');
$results = [];

// Simulate multiple data sources
for ($source = 1; $source <= 3; $source++) {
    for ($item = 1; $item <= 2; $item++) {
        $json = Json::create([
            'source' => $source,
            'item' => $item,
            'value' => $source * $item * 10,
        ]);
        
        $results[] = $json->toArray();
    }
}

echo "Combined " . count($results) . " items from multiple sources:\n";
foreach ($results as $result) {
    echo "  Source " . $result['source'] . ", Item " . $result['item'] . ": " . $result['value'] . "\n";
}

// Example 10: Practical - Process API response stream
print_section('10. Practical: Process API Response Stream');
// Simulate API responses coming one at a time
$responses = [
    ['id' => 1, 'status' => 'success', 'data' => ['count' => 100]],
    ['id' => 2, 'status' => 'success', 'data' => ['count' => 200]],
    ['id' => 3, 'status' => 'error', 'message' => 'Failed'],
    ['id' => 4, 'status' => 'success', 'data' => ['count' => 150]],
];

$totalCount = 0;
$successCount = 0;
$errorCount = 0;

foreach ($responses as $response) {
    $json = Json::create($response);
    
    if ($json->get('status') === 'success') {
        $successCount++;
        $totalCount += $json->get('data.count', 0);
    } else {
        $errorCount++;
    }
}

echo "Processed responses:\n";
echo "  Success: $successCount\n";
echo "  Errors: $errorCount\n";
echo "  Total items: $totalCount\n";

print_section('Examples Complete');
