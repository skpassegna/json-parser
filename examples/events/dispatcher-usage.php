<?php

declare(strict_types=1);

/**
 * Event System and Dispatcher
 *
 * This example demonstrates how to use the event dispatcher system
 * to listen to JSON operations and lifecycle events.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Skpassegna\Json\Json;
use Skpassegna\Json\Enums\EventType;

echo "=== Event System and Dispatcher ===\n\n";

// Example 1: Subscribe to events
echo "1. Subscribe to BEFORE_MERGE and AFTER_MERGE events:\n";
$json = Json::parse('{"name": "Alice", "age": 30}');

$dispatcher = $json->getDispatcher();

$dispatcher->subscribe(EventType::BEFORE_MERGE->value, function ($event) {
    echo "   [Event] BEFORE_MERGE triggered\n";
    echo "   [Event] Current data: " . json_encode($event->getPayload()['operand1'] ?? $event->getData()) . "\n";
});

$dispatcher->subscribe(EventType::AFTER_MERGE->value, function ($event) {
    echo "   [Event] AFTER_MERGE triggered\n";
    echo "   [Event] Merge result received\n";
});

echo "   Performing merge operation...\n";
$json->merge(['city' => 'New York']);
echo "\n";

// Example 2: Priority-based event listeners
echo "2. Priority-based event listeners (higher priority executes first):\n";
$json2 = Json::parse('{"value": 10}');
$dispatcher2 = $json2->getDispatcher();

$dispatcher2->subscribe(EventType::BEFORE_MERGE->value, function ($event) {
    echo "   [Priority 50] First handler\n";
}, 50);

$dispatcher2->subscribe(EventType::BEFORE_MERGE->value, function ($event) {
    echo "   [Priority 100] Second handler (higher priority)\n";
}, 100);

$dispatcher2->subscribe(EventType::BEFORE_MERGE->value, function ($event) {
    echo "   [Priority 10] Third handler (lowest priority)\n";
}, 10);

echo "   Performing merge (handlers execute by priority)...\n";
$json2->merge(['extra' => 'data']);
echo "\n";

// Example 3: Reflection with listener information
echo "3. Reflection with listener information:\n";
$json3 = Json::parse('{"users": [{"id": 1, "name": "Alice"}, {"id": 2, "name": "Bob"}]}');
$dispatcher3 = $json3->getDispatcher();

// Add some listeners
$dispatcher3->subscribe(EventType::BEFORE_PARSE->value, fn($e) => null);
$dispatcher3->subscribe(EventType::AFTER_PARSE->value, fn($e) => null);
$dispatcher3->subscribe(EventType::BEFORE_VALIDATE->value, fn($e) => null);

$inspector = $json3->reflect(true);
$description = $inspector->describeDocument();

echo "   Document structure:\n";
echo "     Type: " . $description['type'] . "\n";
echo "     Size: " . $description['size'] . " bytes\n";
echo "     Depth: " . $description['depth'] . " levels\n\n";

echo "   Event listeners registered:\n";
$listenerInfo = $inspector->getListenerInfo();
foreach ($listenerInfo as $eventType => $listeners) {
    echo "     - " . $eventType . ": " . count($listeners) . " listener(s)\n";
}
echo "\n";

// Example 4: Stop event propagation
echo "4. Stop event propagation:\n";
$json4 = Json::parse('{"data": "test"}');
$dispatcher4 = $json4->getDispatcher();

$handlerCount = 0;

$dispatcher4->subscribe(EventType::BEFORE_MERGE->value, function ($event) use (&$handlerCount) {
    $handlerCount++;
    echo "   [Handler 1] Processing...\n";
    $event->stopPropagation();
    echo "   [Handler 1] Stopped propagation\n";
});

$dispatcher4->subscribe(EventType::BEFORE_MERGE->value, function ($event) use (&$handlerCount) {
    $handlerCount++;
    echo "   [Handler 2] This should not execute\n";
});

echo "   Performing merge with propagation stopping...\n";
$json4->merge(['new' => 'data']);
echo "   Total handlers executed: $handlerCount\n\n";

echo "Event system examples completed!\n";
