<?php

/**
 * File Operations
 *
 * Demonstrates reading JSON from files and writing JSON to files.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;

// Example 1: Load from file
print_section('1. Load JSON from File');
$usersFile = get_example_file('users.json');
if (file_exists($usersFile)) {
    $usersJson = Json::parse(file_get_contents($usersFile));
    echo "Loaded from: $usersFile\n";
    echo "Total users: " . count($usersJson->get('users')) . "\n";
} else {
    echo "Users file not found\n";
}

// Example 2: Create and work with loaded data
print_section('2. Extract Data from Loaded File');
$users = $usersJson->get('users');
foreach ($users as $index => $user) {
    echo ($index + 1) . ". " . $user['name'] . " (" . $user['email'] . ")\n";
}

// Example 3: Modify and save
print_section('3. Modify and Prepare for Saving');
$usersJson->set('metadata.last_updated', date('c'));
$usersJson->set('metadata.version', 2);
echo "Added metadata for export\n";

// Example 4: Convert to string for saving
print_section('4. Convert to String for File Output');
$jsonString = $usersJson->toString(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
echo "JSON ready for file output (first 200 chars):\n";
echo substr($jsonString, 0, 200) . "...\n";

// Example 5: Create temporary export
print_section('5. Create Temporary Export File');
$tempFile = sys_get_temp_dir() . '/exported_users_' . uniqid() . '.json';
file_put_contents($tempFile, $jsonString);
echo "Exported to: $tempFile\n";
echo "File size: " . filesize($tempFile) . " bytes\n";

// Cleanup
@unlink($tempFile);
echo "Temporary file cleaned up\n";

// Example 6: Load products
print_section('6. Load Products File');
$productsFile = get_example_file('products.json');
if (file_exists($productsFile)) {
    $productsJson = Json::parse(file_get_contents($productsFile));
    $storeName = $productsJson->get('store.name');
    $productCount = count($productsJson->get('store.products'));
    echo "Store: $storeName\n";
    echo "Products: $productCount\n";
}

// Example 7: Load configuration
print_section('7. Load Configuration File');
$configFile = get_example_file('config.json');
if (file_exists($configFile)) {
    $configJson = Json::parse(file_get_contents($configFile));
    echo "App name: " . $configJson->get('app.name') . "\n";
    echo "Version: " . $configJson->get('app.version') . "\n";
    echo "Debug: " . ($configJson->get('app.debug') ? 'enabled' : 'disabled') . "\n";
}

// Example 8: Save with different formatting
print_section('8. Different JSON Formats');
$sample = Json::create(['id' => 1, 'name' => 'test', 'active' => true]);

echo "Compact format:\n";
echo $sample->toString(0) . "\n\n";

echo "Pretty print:\n";
echo $sample->toString(JSON_PRETTY_PRINT) . "\n\n";

echo "Unescaped slashes:\n";
echo $sample->toString(JSON_UNESCAPED_SLASHES) . "\n";

// Example 9: Read specific config values
print_section('9. Extract Nested Configuration Values');
if (file_exists($configFile)) {
    $config = Json::parse(file_get_contents($configFile));
    $dbHost = $config->get('database.connections.mysql.host');
    $dbPort = $config->get('database.connections.mysql.port');
    $cacheDriver = $config->get('cache.default');
    
    echo "Database Host: $dbHost:$dbPort\n";
    echo "Cache Driver: $cacheDriver\n";
}

// Example 10: Create sample file structure
print_section('10. Create Sample Data File Structure');
$data = [
    'timestamp' => date('c'),
    'version' => '1.0',
    'items' => [
        ['id' => 1, 'name' => 'Item 1', 'value' => 100],
        ['id' => 2, 'name' => 'Item 2', 'value' => 200],
    ],
];

$json = Json::create($data);
echo "Created sample structure:\n";
echo $json->toString(JSON_PRETTY_PRINT) . "\n";

print_section('Examples Complete');
