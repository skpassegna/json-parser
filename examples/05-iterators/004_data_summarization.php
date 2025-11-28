<?php

/**
 * Data Summarization During Iteration
 *
 * Generate summaries by iterating and aggregating.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

print_section('Data Summarization');

$data = Json::parse(file_get_contents(get_example_file('users.json')));

$users = $data->get('users');
$activeCount = 0;
$totalUsers = 0;

foreach ($users as $user) {
    $totalUsers++;
    if ($user['active']) {
        $activeCount++;
    }
}

echo "Total users: $totalUsers\n";
echo "Active: $activeCount\n";
echo "Inactive: " . ($totalUsers - $activeCount) . "\n";

print_section('Examples Complete');
