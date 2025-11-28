<?php

/**
 * Custom Event Handling Patterns
 *
 * Building custom event handling workflows.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;
use Skpassegna\Json\Enums\EventType;

// Example: Custom workflow
print_section('Custom Event Workflow');

$json = Json::create();
$dispatcher = $json->getDispatcher();

$workflow = [];

$dispatcher->subscribe(EventType::BEFORE_MUTATE->value, function($e) use (&$workflow) {
    $workflow[] = 'validation_started';
});

$dispatcher->subscribe(EventType::AFTER_MUTATE->value, function($e) use (&$workflow) {
    $workflow[] = 'save_to_db';
});

$json->set('field', 'value');

echo "Workflow executed:\n";
foreach ($workflow as $step) {
    echo "  1. $step\n";
}

print_section('Examples Complete');
