<?php

/**
 * Magic Property Access - Getters
 *
 * Demonstrates accessing JSON data via magic __get method
 * allowing property-style access to nested data.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Basic magic property access
print_section('1. Basic Magic Property Access');
$json = Json::create([
    'name' => 'John',
    'email' => 'john@example.com',
    'age' => 30,
]);

echo "Name: " . $json->name . "\n";
echo "Email: " . $json->email . "\n";
echo "Age: " . $json->age . "\n";

// Example 2: Magic access to nested objects
print_section('2. Magic Access to Nested Objects');
$json = Json::parse(json_encode([
    'user' => [
        'id' => 1,
        'profile' => [
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'location' => 'NYC',
        ],
    ],
]));

// Convert to object for magic access
$userData = (object)$json->get('user');
echo "User ID: " . $userData->id . "\n";
echo "First name: " . $userData->profile['first_name'] . "\n";

// Example 3: Compare magic vs dot notation
print_section('3. Compare Access Methods');
$data = Json::create([
    'status' => 'active',
    'count' => 42,
    'enabled' => true,
]);

echo "Using dot notation:\n";
echo "  Status: " . $data->get('status') . "\n";

echo "Using magic property:\n";
echo "  Status: " . $data->status . "\n";

echo "Both methods return the same value\n";

// Example 4: Magic access with array conversion
print_section('4. Convert to Object for Magic Access');
$json = Json::create([
    'title' => 'My Article',
    'author' => 'Bob',
    'views' => 1000,
    'published' => true,
]);

// For nested magic access, you can convert to object
$jsonObj = (object)$json->toArray();
echo "Title: " . $jsonObj->title . "\n";
echo "Author: " . $jsonObj->author . "\n";
echo "Views: " . $jsonObj->views . "\n";
echo "Published: " . ($jsonObj->published ? 'yes' : 'no') . "\n";

// Example 5: Working with complex structures
print_section('5. Complex Structure with Magic Access');
$data = Json::create([
    'company' => [
        'name' => 'TechCorp',
        'employees' => 500,
        'ceo' => [
            'name' => 'Jane Doe',
            'email' => 'jane@techcorp.com',
        ],
    ],
]);

// Convert nested structure
$company = (object)$data->get('company');
echo "Company: " . $company->name . "\n";
echo "Employees: " . $company->employees . "\n";

$ceo = (object)$company->ceo;
echo "CEO: " . $ceo->name . " <" . $ceo->email . ">\n";

// Example 6: Magic access in loops
print_section('6. Magic Access in Iteration');
$json = Json::create([
    'items' => [
        ['id' => 1, 'name' => 'Item 1', 'price' => 10.99],
        ['id' => 2, 'name' => 'Item 2', 'price' => 20.99],
        ['id' => 3, 'name' => 'Item 3', 'price' => 15.99],
    ],
]);

$items = $json->get('items');
foreach ($items as $index => $item) {
    $obj = (object)$item;
    echo ($index + 1) . ". " . $obj->name . " - $" . $obj->price . "\n";
}

// Example 7: Check property existence before access
print_section('7. Property Existence Checking');
$json = Json::create([
    'username' => 'alice',
    'email' => 'alice@example.com',
]);

$obj = (object)$json->toArray();

if (isset($obj->username)) {
    echo "Username exists: " . $obj->username . "\n";
}

if (!isset($obj->phone)) {
    echo "Phone property does not exist\n";
}

// Example 8: Magic access with type coercion
print_section('8. Access Different Data Types');
$json = Json::create([
    'string_val' => 'hello',
    'int_val' => 42,
    'float_val' => 3.14,
    'bool_val' => true,
    'null_val' => null,
    'array_val' => [1, 2, 3],
]);

$obj = (object)$json->toArray();

echo "String: " . $obj->string_val . " (type: " . gettype($obj->string_val) . ")\n";
echo "Integer: " . $obj->int_val . " (type: " . gettype($obj->int_val) . ")\n";
echo "Float: " . $obj->float_val . " (type: " . gettype($obj->float_val) . ")\n";
echo "Boolean: " . ($obj->bool_val ? 'true' : 'false') . " (type: " . gettype($obj->bool_val) . ")\n";
echo "Null: " . var_export($obj->null_val, true) . " (type: " . gettype($obj->null_val) . ")\n";
echo "Array count: " . count($obj->array_val) . "\n";

// Example 9: Magic access with default values
print_section('9. Safe Magic Access with Defaults');
$json = Json::create([
    'title' => 'Test',
    'description' => 'A test item',
]);

$obj = (object)$json->toArray();

$title = $obj->title ?? 'Untitled';
$category = $obj->category ?? 'Uncategorized';

echo "Title: $title\n";
echo "Category: $category\n";

// Example 10: Practical example - API response handling
print_section('10. Practical: API Response Handling');
$apiResponse = Json::create([
    'success' => true,
    'data' => [
        'user_id' => 123,
        'username' => 'alice_wonder',
        'email' => 'alice@wonder.com',
        'profile' => [
            'avatar' => 'https://example.com/avatar.jpg',
            'bio' => 'Adventure seeker',
        ],
    ],
]);

$response = (object)$apiResponse->toArray();
$user = (object)$response->data;
$profile = (object)$user->profile;

echo "Success: " . ($response->success ? 'Yes' : 'No') . "\n";
echo "User: " . $user->username . "\n";
echo "Email: " . $user->email . "\n";
echo "Avatar: " . $profile->avatar . "\n";

print_section('Examples Complete');
