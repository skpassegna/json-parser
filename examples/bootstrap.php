<?php

/**
 * Examples Bootstrap File
 *
 * This file provides shared utilities and configuration for all example scripts.
 * It ensures proper autoloading and provides helper functions for data generation.
 */

declare(strict_types=1);

// Load Composer autoloader
$autoloaderPath = dirname(__DIR__) . '/vendor/autoload.php';
if (!file_exists($autoloaderPath)) {
    throw new RuntimeException('Composer autoloader not found. Run `composer install` first.');
}
require_once $autoloaderPath;

use Skpassegna\Json\Json;

/**
 * Helper: Create sample user data
 */
function create_sample_user(int $id = 1, string $name = 'John Doe', int $age = 30): array
{
    return [
        'id' => $id,
        'name' => $name,
        'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
        'age' => $age,
        'active' => true,
        'roles' => ['user', 'moderator'],
        'created_at' => date('Y-m-d H:i:s'),
    ];
}

/**
 * Helper: Create sample API response
 */
function create_sample_api_response(bool $success = true, mixed $data = null, ?string $message = null): array
{
    return [
        'success' => $success,
        'status_code' => $success ? 200 : 400,
        'message' => $message ?? ($success ? 'OK' : 'Error'),
        'data' => $data,
        'timestamp' => date('c'),
    ];
}

/**
 * Helper: Create sample product data
 */
function create_sample_product(int $id = 1, string $name = 'Product', float $price = 99.99): array
{
    return [
        'id' => $id,
        'name' => $name,
        'sku' => 'SKU-' . str_pad((string) $id, 5, '0', STR_PAD_LEFT),
        'price' => $price,
        'currency' => 'USD',
        'stock' => random_int(0, 100),
        'description' => "This is a sample {$name}",
        'category' => 'general',
        'available' => true,
        'updated_at' => date('Y-m-d H:i:s'),
    ];
}

/**
 * Helper: Create sample transaction data
 */
function create_sample_transaction(int $id = 1, float $amount = 100.0, string $status = 'completed'): array
{
    return [
        'id' => 'TXN-' . str_pad((string) $id, 8, '0', STR_PAD_LEFT),
        'amount' => $amount,
        'currency' => 'USD',
        'status' => $status,
        'type' => 'payment',
        'user_id' => random_int(1, 1000),
        'timestamp' => date('c'),
        'metadata' => [
            'source' => 'web',
            'device' => 'desktop',
            'location' => 'US',
        ],
    ];
}

/**
 * Helper: Create sample configuration data
 */
function create_sample_config(): array
{
    return [
        'app' => [
            'name' => 'JSON Parser Example',
            'version' => '1.0.0',
            'debug' => true,
        ],
        'database' => [
            'host' => 'localhost',
            'port' => 5432,
            'name' => 'json_db',
            'pool' => [
                'min' => 2,
                'max' => 10,
            ],
        ],
        'cache' => [
            'driver' => 'redis',
            'ttl' => 3600,
            'prefix' => 'json_',
        ],
        'features' => [
            'streaming' => true,
            'lazy_loading' => true,
            'caching' => true,
            'events' => true,
        ],
    ];
}

/**
 * Helper: Create nested data structure
 */
function create_sample_nested_data(): array
{
    return [
        'organization' => [
            'id' => 1,
            'name' => 'Acme Corp',
            'employees' => [
                [
                    'id' => 101,
                    'name' => 'Alice',
                    'department' => 'Engineering',
                    'salary' => 120000,
                    'skills' => ['PHP', 'Python', 'JavaScript'],
                ],
                [
                    'id' => 102,
                    'name' => 'Bob',
                    'department' => 'Sales',
                    'salary' => 80000,
                    'skills' => ['Communication', 'CRM', 'Negotiation'],
                ],
            ],
            'offices' => [
                [
                    'location' => 'New York',
                    'address' => '123 Main St',
                    'employees_count' => 150,
                ],
                [
                    'location' => 'San Francisco',
                    'address' => '456 Tech Ave',
                    'employees_count' => 200,
                ],
            ],
        ],
    ];
}

/**
 * Helper: Pretty print output with title
 */
function print_section(string $title, mixed $content = null): void
{
    echo "\n";
    echo str_repeat('=', 80) . "\n";
    echo "  {$title}\n";
    echo str_repeat('=', 80) . "\n";
    if ($content !== null) {
        if (is_string($content)) {
            echo $content . "\n";
        } else {
            var_export($content);
        }
    }
}

/**
 * Helper: Print JSON with formatting
 */
function print_json(Json|array $data, string $title = 'Output'): void
{
    print_section($title);
    if ($data instanceof Json) {
        echo $data->toString(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    } else {
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    }
}

/**
 * Helper: Get examples data directory
 */
function get_data_dir(): string
{
    return dirname(__FILE__) . '/data';
}

/**
 * Helper: Get example file path
 */
function get_example_file(string $filename): string
{
    return get_data_dir() . '/' . $filename;
}
