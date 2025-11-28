<?php

/**
 * Collection Operations
 *
 * Advanced collection manipulation and analysis.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example: Collection operations
print_section('Collection Operations');

$data = [
    ['id' => 1, 'score' => 85],
    ['id' => 2, 'score' => 92],
    ['id' => 3, 'score' => 78],
];

$scores = array_column($data, 'score');
$avg = array_sum($scores) / count($scores);
$max = max($scores);
$min = min($scores);

echo "Average: " . number_format($avg, 2) . "\n";
echo "Max: $max\n";
echo "Min: $min\n";

print_section('Examples Complete');
