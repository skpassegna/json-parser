<?php

/**
 * Array Syntax for Modifications
 *
 * Demonstrates using array syntax for complex modifications.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Modify via array syntax
print_section('1. Modify with Array Syntax');

$json = Json::create(['config' => ['debug' => false]]);

$config = $json['config'];
$config['debug'] = true;
$json['config'] = $config;

echo "Debug enabled: " . ($json['config']['debug'] ? 'yes' : 'no') . "\n";

// Example 2: Add nested via array syntax
print_section('2. Add Nested Via Array Syntax');

$json = Json::create();
$json['user'] = ['id' => 1];
$json['user']['settings'] = ['theme' => 'dark'];

print_json($json);

print_section('Examples Complete');
