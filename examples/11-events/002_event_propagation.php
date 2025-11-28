<?php

/**
 * Event Propagation and Lifecycle
 *
 * Demonstrates event lifecycle and stopping propagation.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;
use Skpassegna\Json\Enums\EventType;

// Example 1: Event lifecycle
print_section('1. Event Lifecycle Tracking');

$json = Json::create();
$dispatcher = $json->getDispatcher();

$lifecycle = [];

$dispatcher->subscribe(EventType::BEFORE_MUTATE->value, function($e) use (&$lifecycle) {
    $lifecycle[] = 'before_mutate';
});

$dispatcher->subscribe(EventType::AFTER_MUTATE->value, function($e) use (&$lifecycle) {
    $lifecycle[] = 'after_mutate';
});

$json->set('test', 'value');

echo "Lifecycle:\n";
foreach ($lifecycle as $event) {
    echo "  - $event\n";
}

// Example 2: Multiple operations
print_section('2. Track Multiple Operations');

$json = Json::create();
$dispatcher = $json->getDispatcher();

$eventCount = ['mutations' => 0];

$dispatcher->subscribe(EventType::BEFORE_MUTATE->value, function($e) use (&$eventCount) {
    $eventCount['mutations']++;
});

$json->set('field1', 'value1');
$json->set('field2', 'value2');
$json->set('field3.nested', 'value3');

echo "Total mutations tracked: " . $eventCount['mutations'] . "\n";

print_section('Examples Complete');
