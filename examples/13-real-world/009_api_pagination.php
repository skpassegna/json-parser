<?php

/**
 * Real-World: API Pagination
 *
 * Building and processing paginated API responses.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

print_section('API Pagination');

// Build paginated response
$items = [];
for ($i = 1; $i <= 5; $i++) {
    $items[] = ['id' => $i, 'name' => "Item $i"];
}

$page = 1;
$perPage = 5;
$total = 25;
$totalPages = ceil($total / $perPage);

$response = Json::create([
    'data' => $items,
    'pagination' => [
        'current_page' => $page,
        'per_page' => $perPage,
        'total' => $total,
        'total_pages' => $totalPages,
        'has_next' => $page < $totalPages,
        'has_prev' => $page > 1,
    ],
]);

echo "Current page: " . $response->get('pagination.current_page') . "\n";
echo "Total pages: " . $response->get('pagination.total_pages') . "\n";
echo "Has next: " . ($response->get('pagination.has_next') ? 'yes' : 'no') . "\n";

print_section('Examples Complete');
