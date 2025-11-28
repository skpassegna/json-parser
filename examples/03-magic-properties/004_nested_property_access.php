<?php

/**
 * Nested Property Access
 *
 * Deep property access through objects.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

print_section('Nested Property Access');

$data = Json::parse(file_get_contents(get_example_file('users.json')));
$users = $data->get('users');

if (!empty($users)) {
    $firstUser = (object)$users[0];
    echo "First user: " . $firstUser->name . "\n";
    echo "Email: " . $firstUser->email . "\n";
    
    if (isset($firstUser->metadata)) {
        $meta = (object)$firstUser->metadata;
        echo "Last login: " . $meta->last_login . "\n";
    }
}

print_section('Examples Complete');
