<?php

/**
 * Real-World: Monitoring and Logging
 *
 * Demonstrates using events for monitoring and structured logging.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;
use Skpassegna\Json\Enums\EventType;

// Example 1: Structured logging
print_section('1. Structured Event Logging');

$json = Json::create();
$dispatcher = $json->getDispatcher();

$logs = [];

$dispatcher->subscribe(EventType::BEFORE_MUTATE->value, function($e) use (&$logs) {
    $logs[] = [
        'timestamp' => date('c'),
        'event' => 'before_mutate',
        'level' => 'debug',
    ];
});

$dispatcher->subscribe(EventType::AFTER_MUTATE->value, function($e) use (&$logs) {
    $logs[] = [
        'timestamp' => date('c'),
        'event' => 'after_mutate',
        'level' => 'info',
    ];
});

$json->set('field1', 'value1');
$json->set('field2', 'value2');

echo "Logged events: " . count($logs) . "\n";
foreach ($logs as $log) {
    echo "  - [" . $log['level'] . "] " . $log['event'] . "\n";
}

// Example 2: Performance metrics
print_section('2. Performance Metrics Tracking');

$json = Json::create();
$metrics = [
    'parse_time' => 0,
    'mutations' => 0,
    'total_time' => 0,
];

$startTime = microtime(true);

$json->set('data.field1', 'value1');
$metrics['mutations']++;

$json->set('data.field2', 'value2');
$metrics['mutations']++;

$metrics['total_time'] = (microtime(true) - $startTime) * 1000;

echo "Performance Metrics:\n";
echo "  Mutations: " . $metrics['mutations'] . "\n";
echo "  Total time: " . number_format($metrics['total_time'], 2) . " ms\n";

print_section('Examples Complete');
