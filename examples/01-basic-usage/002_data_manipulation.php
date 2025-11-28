<?php

/**
 * Data Manipulation Operations
 *
 * Demonstrates removing, updating, and querying JSON data structures.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

$data = [
    'user' => [
        'id' => 1,
        'name' => 'Alice',
        'email' => 'alice@example.com',
        'roles' => ['admin', 'user'],
        'settings' => [
            'theme' => 'dark',
            'notifications' => true,
            'language' => 'en',
        ],
    ],
];

$json = Json::create($data);

// Example 1: Remove values
print_section('1. Remove Values');
echo "Before: roles = " . implode(', ', $json->get('user.roles')) . "\n";
$json->remove('user.roles');
echo "After removing user.roles: " . ($json->has('user.roles') ? 'exists' : 'removed') . "\n";

// Example 2: Update nested values
print_section('2. Update Nested Values');
echo "Original theme: " . $json->get('user.settings.theme') . "\n";
$json->set('user.settings.theme', 'light');
echo "Updated theme: " . $json->get('user.settings.theme') . "\n";

// Example 3: Add new nested values
print_section('3. Add New Nested Values');
$json->set('user.profile.bio', 'A software developer');
$json->set('user.profile.location', 'New York');
$json->set('user.profile.joined', '2023-01-15');
echo "Added profile bio: " . $json->get('user.profile.bio') . "\n";
echo "Added profile location: " . $json->get('user.profile.location') . "\n";

// Example 4: Work with arrays
print_section('4. Work with Array Values');
$json->set('user.skills', ['PHP', 'JavaScript', 'Python']);
echo "Skills: " . implode(', ', $json->get('user.skills')) . "\n";

$skills = $json->get('user.skills');
$skills[] = 'Go';
$json->set('user.skills', $skills);
echo "Added Go: " . implode(', ', $json->get('user.skills')) . "\n";

// Example 5: Get subsection of data
print_section('5. Get Subsection of Data');
$userSettings = $json->get('user.settings');
echo "Settings structure:\n";
var_export($userSettings);

// Example 6: Increment/decrement values
print_section('6. Numeric Operations');
$json->set('user.login_count', 10);
echo "Initial count: " . $json->get('user.login_count') . "\n";

$count = $json->get('user.login_count');
$json->set('user.login_count', $count + 1);
echo "After increment: " . $json->get('user.login_count') . "\n";

// Example 7: Check if paths exist before accessing
print_section('7. Safe Path Checking');
$email = $json->has('user.email') ? $json->get('user.email') : 'No email';
$phone = $json->has('user.phone') ? $json->get('user.phone') : 'No phone';
echo "Email: $email\n";
echo "Phone: $phone\n";

// Example 8: Get multiple values at once
print_section('8. Get Multiple Values');
$userId = $json->get('user.id');
$userName = $json->get('user.name');
$userEmail = $json->get('user.email');
echo "User: ID=$userId, Name=$userName, Email=$userEmail\n";

// Example 9: Clear and reset
print_section('9. Clear and Reset');
print_json($json, 'Current structure');

// Example 10: Work with null/missing values
print_section('10. Handle Missing Values');
$missing = $json->get('user.nonexistent');
$withDefault = $json->get('user.nonexistent', 'default_value');
echo "Missing (null): " . var_export($missing, true);
echo "With default: $withDefault\n";

print_section('Examples Complete');
