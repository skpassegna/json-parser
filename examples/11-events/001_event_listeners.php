<?php

/**
 * Event System and Listeners
 *
 * Demonstrates the PSR-14 compatible event system for hooking into
 * JSON operations (parsing, validation, merging, etc.).
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;
use Skpassegna\Json\Enums\EventType;

// Example 1: Subscribe to parse events
print_section('1. Subscribe to Parse Events');
$json = Json::create();
$dispatcher = $json->getDispatcher();

$parseCount = 0;
$dispatcher->subscribe(EventType::BEFORE_PARSE->value, function($event) use (&$parseCount) {
    $parseCount++;
    echo "Before parse event triggered (count: $parseCount)\n";
});

$dispatcher->subscribe(EventType::AFTER_PARSE->value, function($event) {
    echo "After parse event triggered\n";
});

echo "Parsing JSON...\n";
$json = Json::parse('{"name": "test", "value": 123}');
echo "Parse completed\n";

// Example 2: Listen to mutation events
print_section('2. Subscribe to Mutation Events');
$json = Json::create();
$dispatcher = $json->getDispatcher();

$mutationLog = [];
$dispatcher->subscribe(EventType::BEFORE_MUTATE->value, function($event) use (&$mutationLog) {
    $mutationLog[] = ['type' => 'before_mutate', 'time' => date('H:i:s.u')];
});

$dispatcher->subscribe(EventType::AFTER_MUTATE->value, function($event) use (&$mutationLog) {
    $mutationLog[] = ['type' => 'after_mutate', 'time' => date('H:i:s.u')];
});

echo "Setting values...\n";
$json->set('user.name', 'Alice');
$json->set('user.email', 'alice@example.com');

echo "Mutation log:\n";
foreach ($mutationLog as $log) {
    echo "  - " . $log['type'] . " at " . $log['time'] . "\n";
}

// Example 3: Error event handling
print_section('3. Subscribe to Error Events');
$json = Json::create();
$dispatcher = $json->getDispatcher();

$errorOccurred = false;
$dispatcher->subscribe(EventType::PARSE_ERROR->value, function($event) use (&$errorOccurred) {
    $errorOccurred = true;
    echo "Parse error detected\n";
});

// Try to parse invalid JSON (wrapped in try-catch)
echo "Attempting to parse invalid JSON...\n";
try {
    $json = Json::parse('{"invalid json');
} catch (Exception $e) {
    echo "Exception caught: " . $e->getMessage() . "\n";
}

// Example 4: Multiple listeners on same event
print_section('4. Multiple Listeners on Same Event');
$json = Json::create();
$dispatcher = $json->getDispatcher();

$listeners = [];
for ($i = 1; $i <= 3; $i++) {
    $dispatcher->subscribe(EventType::AFTER_MUTATE->value, function($event) use ($i, &$listeners) {
        $listeners[] = "Listener $i called";
    });
}

echo "Setting value (will trigger 3 listeners)...\n";
$json->set('data', 'value');

echo "Listeners triggered:\n";
foreach ($listeners as $listener) {
    echo "  - $listener\n";
}

// Example 5: Event data access
print_section('5. Access Event Data');
$json = Json::create();
$dispatcher = $json->getDispatcher();

$dispatcher->subscribe(EventType::BEFORE_MUTATE->value, function($event) {
    echo "Event type: " . get_class($event) . "\n";
    echo "Event method: " . (method_exists($event, 'getData') ? 'getData available' : 'N/A') . "\n";
});

$json->set('test', 'value');

// Example 6: Listener priorities
print_section('6. Listener Priorities and Order');
$json = Json::create();
$dispatcher = $json->getDispatcher();

$executionOrder = [];

// Add listeners with different priorities
$dispatcher->subscribe(EventType::AFTER_SERIALIZE->value, function($e) use (&$executionOrder) {
    $executionOrder[] = 'Priority 100';
}, 100);

$dispatcher->subscribe(EventType::AFTER_SERIALIZE->value, function($e) use (&$executionOrder) {
    $executionOrder[] = 'Priority 50';
}, 50);

$dispatcher->subscribe(EventType::AFTER_SERIALIZE->value, function($e) use (&$executionOrder) {
    $executionOrder[] = 'Priority 200';
}, 200);

echo "Triggering serialization...\n";
$jsonString = $json->toString();

echo "Execution order (by priority):\n";
foreach ($executionOrder as $item) {
    echo "  - $item\n";
}

// Example 7: Conditional event handling
print_section('7. Conditional Event Handling');
$json = Json::create();
$dispatcher = $json->getDispatcher();

$validations = [];
$dispatcher->subscribe(EventType::BEFORE_VALIDATE->value, function($event) use (&$validations) {
    $validations[] = 'validation_started';
});

$dispatcher->subscribe(EventType::AFTER_VALIDATE->value, function($event) use (&$validations) {
    $validations[] = 'validation_completed';
});

echo "Validating JSON...\n";
// Note: Validation triggers events when explicitly called
$json = Json::create(['id' => 1, 'name' => 'test']);

echo "Validation events:\n";
foreach ($validations as $v) {
    echo "  - $v\n";
}

// Example 8: Event chaining
print_section('8. Event Chaining');
$json = Json::create();
$dispatcher = $json->getDispatcher();

$eventSequence = [];

$dispatcher->subscribe(EventType::BEFORE_PARSE->value, function($e) use (&$eventSequence) {
    $eventSequence[] = 1;
    echo "1. Before Parse\n";
});

$dispatcher->subscribe(EventType::AFTER_PARSE->value, function($e) use (&$eventSequence) {
    $eventSequence[] = 2;
    echo "2. After Parse\n";
});

echo "Parsing...\n";
$json = Json::parse('{"data": "test"}');

echo "Event sequence: " . implode(' -> ', $eventSequence) . "\n";

// Example 9: Logging all events
print_section('9. Log All Events');
$json = Json::create();
$dispatcher = $json->getDispatcher();

$eventLog = [];

// Subscribe to generic error event
$dispatcher->subscribe(EventType::ON_WARNING->value, function($event) use (&$eventLog) {
    $eventLog[] = ['event' => 'warning', 'time' => microtime(true)];
});

$dispatcher->subscribe(EventType::ON_ERROR->value, function($event) use (&$eventLog) {
    $eventLog[] = ['event' => 'error', 'time' => microtime(true)];
});

echo "Performing operations...\n";
$json->set('field', 'value');

echo "Logged events: " . count($eventLog) . "\n";

// Example 10: Practical - Operation tracking
print_section('10. Practical: Operation Tracking');
$json = Json::create();
$dispatcher = $json->getDispatcher();

$stats = [
    'operations' => 0,
    'mutations' => 0,
    'errors' => 0,
];

$dispatcher->subscribe(EventType::BEFORE_MUTATE->value, function($e) use (&$stats) {
    $stats['operations']++;
    $stats['mutations']++;
});

$dispatcher->subscribe(EventType::ON_ERROR->value, function($e) use (&$stats) {
    $stats['errors']++;
});

echo "Performing operations with tracking...\n";
$json->set('user.name', 'Alice');
$json->set('user.email', 'alice@example.com');
$json->set('user.age', 28);

echo "Statistics:\n";
echo "  Total operations: " . $stats['operations'] . "\n";
echo "  Mutations: " . $stats['mutations'] . "\n";
echo "  Errors: " . $stats['errors'] . "\n";

print_section('Examples Complete');
