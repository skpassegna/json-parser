<?php

/**
 * Conditional Building with Fluent Interface
 *
 * Demonstrates conditional data building based on business logic and state.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Build response based on success
print_section('1. Conditional Response Building');

$success = true;
$data = Json::create();

$data->set('timestamp', date('c'));
$data->set('version', '1.0');

if ($success) {
    $data->set('status', 'success')
        ->set('code', 200)
        ->set('data', ['id' => 1, 'created' => true]);
} else {
    $data->set('status', 'error')
        ->set('code', 400)
        ->set('error', 'Operation failed');
}

print_json($data);

// Example 2: Build user with optional fields
print_section('2. Build User with Optional Fields');

$isPremium = true;
$isVerified = false;

$user = Json::create()
    ->set('id', 1)
    ->set('username', 'alice')
    ->set('email', 'alice@example.com');

if ($isPremium) {
    $user->set('subscription.level', 'premium')
        ->set('subscription.expires', date('Y-m-d', strtotime('+1 year')));
}

if ($isVerified) {
    $user->set('verified', true);
}

print_json($user);

// Example 3: Build with role-based fields
print_section('3. Role-Based Field Building');

$userRole = 'admin';

$profile = Json::create()
    ->set('name', 'John')
    ->set('email', 'john@example.com');

if ($userRole === 'admin') {
    $profile->set('permissions', ['read', 'write', 'delete', 'manage_users']);
} elseif ($userRole === 'moderator') {
    $profile->set('permissions', ['read', 'write', 'manage_comments']);
} else {
    $profile->set('permissions', ['read']);
}

print_json($profile);

// Example 4: Accumulate data conditionally
print_section('4. Accumulate Data Conditionally');

$report = Json::create()
    ->set('title', 'Report')
    ->set('generated_at', date('c'));

$metrics = [];
$metrics[] = 'page_views';
$metrics[] = 'conversions';
if (true) {
    $metrics[] = 'revenue';
}

foreach ($metrics as $index => $metric) {
    $report->set("metrics.$index", $metric);
}

print_json($report);

print_section('Examples Complete');
