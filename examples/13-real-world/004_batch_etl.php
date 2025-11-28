<?php

/**
 * Real-World: Batch ETL (Extract, Transform, Load)
 *
 * Demonstrates processing large batches of data through
 * extraction, transformation, and loading stages.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Simple ETL pipeline
print_section('1. Basic ETL Pipeline');

// Extract: Simulate reading from source
$source = [
    ['user_id' => '1001', 'user_name' => 'Alice', 'amount' => '100.00', 'txn_date' => '2025-05-28'],
    ['user_id' => '1002', 'user_name' => 'Bob', 'amount' => '250.50', 'txn_date' => '2025-05-27'],
    ['user_id' => '1003', 'user_name' => 'Charlie', 'amount' => '75.25', 'txn_date' => '2025-05-26'],
];

echo "Extract: " . count($source) . " records\n";

// Transform
$transformed = [];
foreach ($source as $record) {
    $transformed[] = [
        'user_id' => (int)$record['user_id'],
        'user_name' => $record['user_name'],
        'amount' => (float)$record['amount'],
        'transaction_date' => date('Y-m-d', strtotime($record['txn_date'])),
        'processed_at' => date('c'),
    ];
}

echo "Transform: Applied type conversions and formatting\n";

// Load: Save to Json
$loaded = Json::create([
    'batch_id' => 'BATCH-' . date('Ymd'),
    'records_processed' => count($transformed),
    'total_amount' => array_sum(array_column($transformed, 'amount')),
    'data' => $transformed,
]);

print_json($loaded);

// Example 2: ETL with validation
print_section('2. ETL with Validation');

$source = [
    ['id' => 1, 'email' => 'valid@example.com', 'score' => 85],
    ['id' => 2, 'email' => 'invalid', 'score' => 90],
    ['id' => 3, 'email' => 'good@example.com', 'score' => 78],
];

$valid = [];
$invalid = [];

foreach ($source as $record) {
    $errors = [];
    
    // Validate email
    if (!filter_var($record['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email';
    }
    
    // Validate score
    if ($record['score'] < 0 || $record['score'] > 100) {
        $errors[] = 'Score out of range';
    }
    
    if (empty($errors)) {
        $valid[] = $record;
    } else {
        $invalid[] = ['record' => $record, 'errors' => $errors];
    }
}

$result = Json::create([
    'valid_records' => count($valid),
    'invalid_records' => count($invalid),
    'valid_data' => $valid,
    'invalid_data' => $invalid,
]);

echo "Validation result:\n";
echo "  Valid: " . $result->get('valid_records') . "\n";
echo "  Invalid: " . $result->get('invalid_records') . "\n";

// Example 3: Batch aggregation
print_section('3. Batch Aggregation');

$transactions = [
    ['id' => 1, 'customer_id' => 101, 'amount' => 50, 'date' => '2025-05-01'],
    ['id' => 2, 'customer_id' => 102, 'amount' => 75, 'date' => '2025-05-01'],
    ['id' => 3, 'customer_id' => 101, 'amount' => 25, 'date' => '2025-05-02'],
    ['id' => 4, 'customer_id' => 103, 'amount' => 100, 'date' => '2025-05-02'],
];

$aggregated = [];

foreach ($transactions as $txn) {
    $custId = $txn['customer_id'];
    
    if (!isset($aggregated[$custId])) {
        $aggregated[$custId] = [
            'customer_id' => $custId,
            'transaction_count' => 0,
            'total_amount' => 0,
        ];
    }
    
    $aggregated[$custId]['transaction_count']++;
    $aggregated[$custId]['total_amount'] += $txn['amount'];
}

$result = Json::create([
    'customer_summary' => array_values($aggregated),
]);

echo "Aggregated by customer:\n";
foreach ($result->get('customer_summary') as $summary) {
    echo "  Customer " . $summary['customer_id'] . ": " . $summary['transaction_count'];
    echo " txns, total: \$" . $summary['total_amount'] . "\n";
}

print_section('Examples Complete');
