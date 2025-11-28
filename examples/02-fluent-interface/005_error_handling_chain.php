<?php

/**
 * Error Handling in Fluent Chains
 *
 * Safe method chaining with error handling.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example: Safe chaining
print_section('Safe Chaining');

$json = Json::create();

try {
    $json->set('field', 'value')
        ->set('nested.field', 'nested_value');
    
    print_json($json);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

print_section('Examples Complete');
