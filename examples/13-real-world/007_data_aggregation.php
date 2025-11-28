<?php

/**
 * Real-World: Data Aggregation and Reporting
 *
 * Aggregate data for reporting and analytics.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Sales report
print_section('1. Generate Sales Report');

$transactions = [
    ['product' => 'A', 'amount' => 100, 'date' => '2025-05-01'],
    ['product' => 'B', 'amount' => 150, 'date' => '2025-05-01'],
    ['product' => 'A', 'amount' => 120, 'date' => '2025-05-02'],
    ['product' => 'C', 'amount' => 200, 'date' => '2025-05-02'],
    ['product' => 'A', 'amount' => 80, 'date' => '2025-05-03'],
];

$report = Json::create([
    'generated_at' => date('c'),
    'period' => '2025-05-01 to 2025-05-03',
]);

// Aggregate by product
$byProduct = [];
foreach ($transactions as $t) {
    if (!isset($byProduct[$t['product']])) {
        $byProduct[$t['product']] = ['sales' => 0, 'count' => 0];
    }
    $byProduct[$t['product']]['sales'] += $t['amount'];
    $byProduct[$t['product']]['count']++;
}

foreach ($byProduct as $product => $data) {
    $report->set("products.$product.total", $data['sales']);
    $report->set("products.$product.transactions", $data['count']);
}

$totalSales = array_sum(array_column($transactions, 'amount'));
$report->set('total_sales', $totalSales);
$report->set('total_transactions', count($transactions));

print_json($report);

// Example 2: User analytics
print_section('2. User Analytics');

$users = [
    ['id' => 1, 'active' => true, 'joined' => '2024-01-01'],
    ['id' => 2, 'active' => true, 'joined' => '2024-02-15'],
    ['id' => 3, 'active' => false, 'joined' => '2024-03-01'],
    ['id' => 4, 'active' => true, 'joined' => '2024-04-20'],
];

$analytics = Json::create([
    'total_users' => count($users),
    'active_users' => count(array_filter($users, fn($u) => $u['active'])),
    'inactive_users' => count(array_filter($users, fn($u) => !$u['active'])),
]);

echo "User Analytics:\n";
echo "  Total: " . $analytics->get('total_users') . "\n";
echo "  Active: " . $analytics->get('active_users') . "\n";
echo "  Inactive: " . $analytics->get('inactive_users') . "\n";

print_section('Examples Complete');
