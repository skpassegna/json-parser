<?php

/**
 * Real-World: Form Data Processing
 *
 * Processing and validating form submissions.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example: Process form
print_section('Form Processing');

$formData = [
    'name' => ' John Doe ',
    'email' => 'john@example.com',
    'age' => '25',
    'subscribe' => '1',
];

$processed = Json::create();

// Sanitize and process
$processed->set('name', trim($formData['name']))
    ->set('email', strtolower($formData['email']))
    ->set('age', (int)$formData['age'])
    ->set('subscribe', (bool)$formData['subscribe']);

print_json($processed);

print_section('Examples Complete');
