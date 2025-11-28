<?php

/**
 * Real-World: Configuration Bootstrap
 *
 * Demonstrates loading environment-specific configurations,
 * merging defaults with overrides, and validating settings.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Skpassegna\Json\Json;
use Skpassegna\Json\Enums\DiffMergeStrategy;

// Example 1: Basic config loading
print_section('1. Load and Parse Configuration');

$configFile = get_example_file('config.json');
if (file_exists($configFile)) {
    $config = Json::parse(file_get_contents($configFile));
    
    echo "Configuration loaded:\n";
    echo "  App: " . $config->get('app.name') . " v" . $config->get('app.version') . "\n";
    echo "  Debug: " . ($config->get('app.debug') ? 'enabled' : 'disabled') . "\n";
    echo "  Database: " . $config->get('database.default') . "\n";
}

// Example 2: Multi-environment config merge
print_section('2. Environment-Specific Configuration Merge');

$defaultConfig = [
    'app' => ['name' => 'MyApp', 'debug' => false],
    'database' => ['host' => 'localhost', 'port' => 5432],
    'cache' => ['driver' => 'file', 'ttl' => 3600],
];

$productionOverride = [
    'app' => ['debug' => false],
    'database' => ['host' => 'prod.db.example.com'],
    'cache' => ['driver' => 'redis', 'ttl' => 7200],
];

$developmentOverride = [
    'app' => ['debug' => true],
    'database' => ['host' => 'localhost', 'port' => 5433],
];

$env = 'production';
$config = Json::create($defaultConfig);

if ($env === 'production') {
    $config->mergeWithStrategy($productionOverride, DiffMergeStrategy::MERGE_RECURSIVE);
} elseif ($env === 'development') {
    $config->mergeWithStrategy($developmentOverride, DiffMergeStrategy::MERGE_RECURSIVE);
}

echo "Loaded configuration for: $env\n";
print_json($config);

// Example 3: Feature flags configuration
print_section('3. Feature Flags Configuration');

$featureFlags = [
    'authentication' => true,
    'api_v2' => true,
    'analytics' => true,
    'beta_features' => false,
    'experimental' => false,
];

$config = Json::create(['features' => $featureFlags]);

echo "Feature Flags:\n";
$features = $config->get('features');
foreach ($features as $feature => $enabled) {
    echo "  " . str_pad($feature, 20) . ": " . ($enabled ? '✓ Enabled' : '✗ Disabled') . "\n";
}

// Example 4: Database connection pooling config
print_section('4. Database Connection Configuration');

$dbConfig = Json::create([
    'connections' => [
        'primary' => [
            'host' => 'db-primary.example.com',
            'port' => 5432,
            'database' => 'production',
            'pool' => [
                'min_size' => 5,
                'max_size' => 20,
                'idle_timeout' => 300,
            ],
        ],
        'replica' => [
            'host' => 'db-replica.example.com',
            'port' => 5432,
            'database' => 'production',
            'pool' => [
                'min_size' => 3,
                'max_size' => 10,
                'idle_timeout' => 600,
            ],
        ],
    ],
]);

echo "Database Configuration:\n";
foreach ($dbConfig->get('connections') as $name => $conn) {
    echo "  $name:\n";
    echo "    Host: " . $conn['host'] . ":" . $conn['port'] . "\n";
    echo "    Pool: " . $conn['pool']['min_size'] . "-" . $conn['pool']['max_size'] . "\n";
}

// Example 5: Logging configuration
print_section('5. Logging Configuration');

$loggingConfig = Json::create([
    'default' => 'stack',
    'channels' => [
        'single' => [
            'driver' => 'single',
            'path' => '/var/log/app.log',
            'level' => 'debug',
        ],
        'daily' => [
            'driver' => 'daily',
            'path' => '/var/log/app.log',
            'level' => 'info',
            'days' => 14,
        ],
        'slack' => [
            'driver' => 'slack',
            'url' => 'https://hooks.slack.com/...',
            'level' => 'critical',
        ],
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily', 'slack'],
        ],
    ],
]);

echo "Logging Channels:\n";
$channels = $loggingConfig->get('channels');
foreach ($channels as $name => $channel) {
    if ($channel['driver'] === 'stack') {
        echo "  $name (stack): " . implode(', ', $channel['channels']) . "\n";
    } else {
        echo "  $name (" . $channel['driver'] . "): level=" . $channel['level'] . "\n";
    }
}

// Example 6: Service configuration with defaults
print_section('6. Service Configuration with Validation');

$serviceDefaults = [
    'timeout' => 30,
    'retries' => 3,
    'backoff' => 'exponential',
    'backoff_multiplier' => 2,
    'cache_responses' => true,
    'cache_ttl' => 3600,
];

$serviceName = 'PaymentGateway';
$serviceOverride = [
    'timeout' => 60,
    'retries' => 5,
    'cache_ttl' => 7200,
];

$serviceConfig = Json::create($serviceDefaults);
$serviceConfig->mergeWithStrategy($serviceOverride, DiffMergeStrategy::MERGE_RECURSIVE);

echo "Service Configuration: $serviceName\n";
foreach ($serviceConfig->toArray() as $key => $value) {
    echo "  " . str_pad($key, 20) . ": " . var_export($value, true) . "\n";
}

// Example 7: API rate limiting config
print_section('7. API Rate Limiting Configuration');

$rateLimitConfig = Json::create([
    'enabled' => true,
    'default' => [
        'requests_per_minute' => 60,
        'burst_size' => 10,
    ],
    'endpoints' => [
        'search' => [
            'requests_per_minute' => 30,
            'burst_size' => 5,
        ],
        'upload' => [
            'requests_per_minute' => 10,
            'burst_size' => 2,
        ],
    ],
]);

echo "Rate Limiting Configuration:\n";
echo "Enabled: " . ($rateLimitConfig->get('enabled') ? 'yes' : 'no') . "\n";
echo "Default: " . $rateLimitConfig->get('default.requests_per_minute') . " req/min\n";
echo "Endpoints:\n";
foreach ($rateLimitConfig->get('endpoints') as $endpoint => $limit) {
    echo "  /$endpoint: " . $limit['requests_per_minute'] . " req/min\n";
}

// Example 8: Security configuration
print_section('8. Security Configuration');

$securityConfig = Json::create([
    'cors' => [
        'enabled' => true,
        'origins' => ['https://example.com', 'https://app.example.com'],
        'methods' => ['GET', 'POST', 'PUT', 'DELETE'],
        'credentials' => true,
    ],
    'jwt' => [
        'enabled' => true,
        'secret' => 'secret_key_here',
        'algorithm' => 'HS256',
        'exp' => 86400,
    ],
    'https' => [
        'redirect' => true,
        'hsts_max_age' => 31536000,
    ],
]);

echo "Security Configuration:\n";
echo "CORS: " . ($securityConfig->get('cors.enabled') ? 'enabled' : 'disabled') . "\n";
echo "JWT: " . ($securityConfig->get('jwt.enabled') ? 'enabled' : 'disabled') . "\n";
echo "HTTPS Redirect: " . ($securityConfig->get('https.redirect') ? 'yes' : 'no') . "\n";

// Example 9: Config validation
print_section('9. Configuration Validation');

$config = Json::create([
    'app_name' => 'MyApp',
    'port' => 8080,
    'debug' => true,
]);

// Validate required fields
$requiredFields = ['app_name', 'port'];
$valid = true;
$errors = [];

foreach ($requiredFields as $field) {
    if (!$config->has($field)) {
        $valid = false;
        $errors[] = "Missing required field: $field";
    }
}

// Validate types
if ($config->has('port') && !is_int($config->get('port'))) {
    $valid = false;
    $errors[] = "Port must be an integer";
}

echo "Configuration validation:\n";
echo "Valid: " . ($valid ? 'yes' : 'no') . "\n";
if (!empty($errors)) {
    echo "Errors:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}

// Example 10: Configuration export for deployment
print_section('10. Export Configuration for Deployment');

$productionConfig = Json::create([
    'app' => ['name' => 'Production App', 'version' => '1.0.0'],
    'database' => ['host' => 'prod.db.com', 'port' => 5432],
    'cache' => ['driver' => 'redis'],
]);

// Export as environment-compatible format
$envConfig = [];
foreach ($productionConfig->toArray() as $section => $values) {
    foreach ((array)$values as $key => $value) {
        $envKey = strtoupper($section . '_' . $key);
        $envConfig[$envKey] = $value;
    }
}

echo "Exported environment variables:\n";
foreach ($envConfig as $key => $value) {
    if (!is_array($value)) {
        echo "  $key=" . var_export($value, true) . "\n";
    }
}

print_section('Examples Complete');
