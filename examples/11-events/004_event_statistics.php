<?php

/**
 * Event Statistics and Tracking
 *
 * Track and analyze event patterns.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;
use Skpassegna\Json\Enums\EventType;

print_section('Event Statistics');

$json = Json::create();
$dispatcher = $json->getDispatcher();

$eventStats = [];

$dispatcher->subscribe(EventType::BEFORE_MUTATE->value, function($e) use (&$eventStats) {
    if (!isset($eventStats['mutations'])) $eventStats['mutations'] = 0;
    $eventStats['mutations']++;
});

// Perform operations
$json->set('field1', 'value1');
$json->set('field2', 'value2');
$json->set('field3', 'value3');

echo "Event Statistics:\n";
foreach ($eventStats as $event => $count) {
    echo "  $event: $count\n";
}

print_section('Examples Complete');
