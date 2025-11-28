<?php

/**
 * Basic Parsing and Access
 *
 * This example demonstrates how to parse JSON strings and access data
 * using the fluent Json class. Shows basic get/set operations.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Parse JSON string
$jsonString = '{"user": {"name": "John Doe", "email": "john@example.com", "age": 30}}';
$json = Json::parse($jsonString);

print_section('1. Parse JSON String');
echo "Original: $jsonString\n";
echo "Parsed as Json instance\n";

// Example 2: Access data using dot notation
print_section('2. Access Data with Dot Notation');
echo "User name: " . $json->get('user.name') . "\n";
echo "User email: " . $json->get('user.email') . "\n";
echo "User age: " . $json->get('user.age') . "\n";

// Example 3: Check if path exists
print_section('3. Check Path Existence');
echo "Has user.name: " . ($json->has('user.name') ? 'yes' : 'no') . "\n";
echo "Has user.phone: " . ($json->has('user.phone') ? 'yes' : 'no') . "\n";
echo "Has non.existent.path: " . ($json->has('non.existent.path') ? 'yes' : 'no') . "\n";

// Example 4: Set/Update values
print_section('4. Set/Update Values');
$json->set('user.email', 'newemail@example.com');
$json->set('user.phone', '+1-555-0123');
echo "Updated email: " . $json->get('user.email') . "\n";
echo "Added phone: " . $json->get('user.phone') . "\n";

// Example 5: Create new data
print_section('5. Create New Json Instance');
$newJson = Json::create()
    ->set('status', 'success')
    ->set('code', 200)
    ->set('message', 'Operation completed');

echo "Created new JSON:\n";
echo $newJson->toString(JSON_PRETTY_PRINT) . "\n";

// Example 6: Get with default values
print_section('6. Get with Default Values');
$phone = $json->get('user.phone', 'Not provided');
$fax = $json->get('user.fax', 'Not provided');
echo "Phone: $phone\n";
echo "Fax: $fax\n";

// Example 7: Get all data
print_section('7. Get Complete Data Structure');
print_json($json, 'Full JSON Structure');

// Example 8: Convert to string
print_section('8. Convert to String');
$compact = $json->toString();
$pretty = $json->toString(JSON_PRETTY_PRINT);
echo "Compact: " . substr($compact, 0, 60) . "...\n";
echo "Pretty print outputs nicely formatted JSON\n";

// Example 9: Create from array
print_section('9. Create from Array');
$data = [
    'id' => 123,
    'title' => 'Sample Article',
    'tags' => ['php', 'json', 'tutorial'],
    'published' => true,
];
$jsonFromArray = Json::create($data);
echo "Created from array:\n";
echo $jsonFromArray->toString(JSON_PRETTY_PRINT) . "\n";

// Example 10: Clone and modify
print_section('10. Clone and Modify');
$original = Json::parse('{"status": "active", "count": 5}');
$clone = Json::create($original->toArray());
$clone->set('status', 'inactive');
echo "Original status: " . $original->get('status') . "\n";
echo "Clone status: " . $clone->get('status') . "\n";

print_section('Examples Complete');
