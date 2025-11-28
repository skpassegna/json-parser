# Examples

This directory contains comprehensive examples demonstrating the features and usage patterns of the JSON Parser library.

## Running Examples

All examples can be run directly from the command line:

```bash
php examples/<category>/<example>.php
```

## Example Categories

### Streaming (`streaming/`)

Learn how to efficiently process large JSON files without loading them entirely into memory.

- **`basic-streaming.php`** - Basic streaming concepts
  ```bash
  php examples/streaming/basic-streaming.php
  ```

- **`parse_large.php`** - Streaming parsing of large JSON files
  ```bash
  php examples/streaming/parse_large.php
  ```

- **`lazy-loading.php`** - Deferred parsing with lazy loading
  ```bash
  php examples/streaming/lazy-loading.php
  ```

- **`caching-queries.php`** - Query result caching
  ```bash
  php examples/streaming/caching-queries.php
  ```

### Procedural API (`procedural/`)

Use the procedural API functions for a functional programming style without object orientation.

- **`basic.php`** - Basic procedural API usage
  ```bash
  php examples/procedural/basic.php
  ```

- **`merge-diff.php`** - Merge and diff operations with strategies
  ```bash
  php examples/procedural/merge-diff.php
  ```

### Type Coercion (`coercion/`)

Master type conversion and normalization with strict and lenient modes.

- **`type-conversion.php`** - Type coercion examples
  ```bash
  php examples/coercion/type-conversion.php
  ```

### Event System (`events/`)

Work with the event dispatcher for lifecycle hooks and monitoring.

- **`dispatcher-usage.php`** - Event dispatcher and listeners
  ```bash
  php examples/events/dispatcher-usage.php
  ```

### Security (`security/`)

Learn security best practices and input validation techniques.

- **`input-validation.php`** - Input validation and sanitization
  ```bash
  php examples/security/input-validation.php
  ```

### Performance (`performance/`)

Optimize JSON operations with caching, lazy loading, and streaming.

- **`caching-optimization.php`** - Query caching and performance tips
  ```bash
  php examples/performance/caching-optimization.php
  ```

## OOP vs Procedural Comparison

### Object-Oriented Approach
```php
use Skpassegna\Json\Json;

$json = Json::parse('{"name": "Alice"}');
$json->set('age', 30);
$result = $json->toString();
```

### Procedural Approach
```php
use function Skpassegna\Json\Procedural\{json_parse, json_set, json_stringify};

$json = json_parse('{"name": "Alice"}');
json_set($json, 'age', 30);
$result = json_stringify($json);
```

Both approaches are equally valid and delegate to the same underlying implementation, ensuring consistency and maintainability.

## Recommended Reading Order

1. Start with **`procedural/basic.php`** to learn fundamental operations
2. Explore **`security/input-validation.php`** to understand security implications
3. Try **`procedural/merge-diff.php`** for advanced operations
4. Review **`events/dispatcher-usage.php`** for lifecycle hooks
5. Study **`streaming/parse_large.php`** for large file handling
6. Check **`performance/caching-optimization.php`** for optimization techniques

## Real-World Use Cases

### REST API Response Processing
```bash
php examples/procedural/basic.php  # Process API responses
```

### Configuration Management
```bash
php examples/procedural/merge-diff.php  # Merge configurations
```

### Data Transformation and Validation
```bash
php examples/security/input-validation.php  # Validate before processing
```

### Large File Processing
```bash
php examples/streaming/parse_large.php  # Handle large datasets
```

## Contributing Examples

When adding new examples:

1. Place them in the appropriate category directory
2. Add clear comments explaining what the example demonstrates
3. Include terminal command examples in output
4. Update this README with the new example
5. Ensure the example is runnable without external dependencies (uses only library features)

## Troubleshooting

If you encounter issues running examples:

1. Ensure Composer dependencies are installed: `composer install`
2. Check PHP version compatibility: `php --version` (PHP 8.0+)
3. Verify the library is properly autoloaded

For questions or issues, please refer to the main [README.md](../README.md) and [CONTRIBUTING.md](../CONTRIBUTING.md).
