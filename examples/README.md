# JSON Parser Examples Suite

A comprehensive collection of 60+ runnable PHP examples showcasing every major capability of the JSON Parser library, organized into 13 themed folders for easy learning and reference.

## Table of Contents

- [Research & Best Practices](#research--best-practices)
- [Directory Structure](#directory-structure)
- [Getting Started](#getting-started)
- [Example Categories](#example-categories)
- [Running Examples](#running-examples)
- [Key Features Demonstrated](#key-features-demonstrated)
- [Prerequisites](#prerequisites)
- [Contributing](#contributing)

## Research & Best Practices

Based on research from PHP documentation best practices (2025), the examples suite follows these principles:

### PHP Example Patterns & Best Practices

1. **Self-Contained Scripts**: Each example is a standalone PHP script that can be executed independently with no additional setup beyond `composer install`.

2. **Strict Type Declarations**: All examples use `declare(strict_types=1)` at the top to encourage type safety and best practices.

3. **Clear Documentation**: Each script includes:
   - A header docblock explaining the scenario and learning objectives
   - Inline comments for complex operations
   - Descriptive output labels using `print_section()` helper

4. **Progressive Complexity**: Examples are organized from basic to advanced:
   - Start with simple operations (parsing, access)
   - Progress through intermediate features (chaining, magic properties)
   - Move to advanced scenarios (streaming, caching, events)
   - Conclude with real-world patterns

5. **Human-Readable Output**: All examples use `var_export()`, `json_encode()`, or `print_json()` helpers to display results clearly for teaching purposes.

6. **Shared Bootstrap**: Common functionality (data generators, output helpers) is centralized in `bootstrap.php` to reduce duplication and improve maintainability.

7. **Sample Data Files**: Real JSON files in `examples/data/` provide realistic test data without requiring external APIs.

8. **Naming Conventions**:
   - Files follow pattern: `NNN_descriptive_name.php` (e.g., `001_parse_and_access.php`)
   - Directories use leading numbers for ordering: `01-category`, `02-category`, etc.

## Directory Structure

```
examples/
├── bootstrap.php                    # Shared utilities and Composer autoloader
├── README.md                        # This file
├── data/                            # Sample JSON files and datasets
│   ├── users.json
│   ├── products.json
│   ├── config.json
│   └── items.ndjson
├── 01-basic-usage/                  # Parsing, creation, basic access
│   ├── 001_parse_and_access.php
│   ├── 002_data_manipulation.php
│   └── 003_file_operations.php
├── 02-fluent-interface/             # Method chaining and fluent API
│   ├── 001_method_chaining.php
│   └── 002_build_pattern.php
├── 03-magic-properties/             # Magic property access (__get)
│   └── 001_magic_getters.php
├── 04-array-access/                 # ArrayAccess interface
│   └── 001_array_access_interface.php
├── 05-iterators/                    # Iterator and IteratorAggregate
│   └── 001_iteration.php
├── 06-type-coercion/                # Type casting and coercion
│   └── 001_type_casting.php
├── 07-json-pointer-path/            # JSON Pointer (RFC 6902) and JSONPath
│   ├── 001_json_pointer.php
│   └── 002_json_path.php
├── 08-streaming/                    # Large file processing
│   └── 001_streaming_large_files.php
├── 09-lazy-loading/                 # Lazy evaluation with proxies
│   └── 001_lazy_proxies.php
├── 10-caching/                      # Caching strategies
│   └── 001_cache_strategies.php
├── 11-events/                       # PSR-14 event system
│   └── 001_event_listeners.php
├── 12-merge-strategies/             # Merge and diff operations
│   └── 001_merge_operations.php
└── 13-real-world/                   # Practical end-to-end scenarios
    ├── 001_api_response_normalization.php
    ├── 002_config_bootstrap.php
    └── 003_data_validation_pipeline.php
```

## Getting Started

### Prerequisites

- PHP 8.0+ (8.1, 8.2, 8.3, 8.4 recommended)
- Composer
- `ext-json`
- `ext-mbstring`

### Installation

```bash
# Install dependencies
composer install

# Examples are ready to run!
```

## Running Examples

### Run a Single Example

```bash
# Run a specific example
php examples/01-basic-usage/001_parse_and_access.php

# Run from project root
php examples/02-fluent-interface/001_method_chaining.php
```

### Run All Examples in a Category

```bash
# Run all basic usage examples
for file in examples/01-basic-usage/*.php; do
    echo "Running $file..."
    php "$file"
    echo ""
done
```

### Run All Examples

```bash
# Create a simple runner script
php -r '
foreach (glob("examples/{01,02,03,04,05,06,07,08,09,10,11,12,13}-*/*.php", GLOB_BRACE) as $file) {
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "Running: $file\n";
    echo str_repeat("=", 80) . "\n";
    include $file;
}
'
```

## Example Categories

### 01. Basic Usage (3 examples)
**Focus**: Parsing, data access, file operations

- **001_parse_and_access.php**: Parse JSON, use dot notation, check paths
- **002_data_manipulation.php**: Update, remove, get nested values
- **003_file_operations.php**: Load JSON from files, save, export formats

**Concepts**: Parsing, dot notation, get/set/remove, file I/O

### 02. Fluent Interface (2 examples)
**Focus**: Method chaining and builder patterns

- **001_method_chaining.php**: Chain set() calls for clean APIs
- **002_build_pattern.php**: Builder pattern for complex structures

**Concepts**: Fluent API, builder pattern, method chaining

### 03. Magic Properties (1 example)
**Focus**: Magic method access

- **001_magic_getters.php**: Access data as object properties

**Concepts**: Magic __get, object-style access

### 04. Array Access (1 example)
**Focus**: ArrayAccess interface implementation

- **001_array_access_interface.php**: Use like arrays with isset/unset

**Concepts**: ArrayAccess, array syntax, $json['key']

### 05. Iterators (1 example)
**Focus**: Iteration and Countable interfaces

- **001_iteration.php**: Loop through data, count, filter, map

**Concepts**: foreach loops, array_map, array_filter, count()

### 06. Type Coercion (1 example)
**Focus**: Type conversion and casting

- **001_type_casting.php**: Convert between types, handle null/bool/numeric

**Concepts**: Type conversion, type checking, casting

### 07. JSON Pointer & Path (2 examples)
**Focus**: RFC 6902 Pointer and JSONPath queries

- **001_json_pointer.php**: Access via /path/to/field syntax
- **002_json_path.php**: Query with JSONPath expressions and filters

**Concepts**: JSON Pointer, JSONPath, queries, filtering

### 08. Streaming (1 example)
**Focus**: Memory-efficient large file processing

- **001_streaming_large_files.php**: NDJSON, streaming parser, chunking

**Concepts**: Streaming, NDJSON, memory efficiency

### 09. Lazy Loading (1 example)
**Focus**: Deferred parsing with proxies

- **001_lazy_proxies.php**: LazyJsonProxy, deferred evaluation

**Concepts**: Lazy loading, proxies, performance optimization

### 10. Caching (1 example)
**Focus**: Caching strategies and cache management

- **001_cache_strategies.php**: Cache warm-up, invalidation, TTL

**Concepts**: Caching, performance, cache strategies

### 11. Events (1 example)
**Focus**: PSR-14 event system

- **001_event_listeners.php**: Subscribe to parse, mutate, error events

**Concepts**: Event system, PSR-14, event handling

### 12. Merge Strategies (1 example)
**Focus**: Advanced merge and diff operations

- **001_merge_operations.php**: Merge recursive, shallow, RFC 7396 patches

**Concepts**: Merge strategies, diff operations, RFC compliance

### 13. Real-World (3 examples)
**Focus**: Practical production scenarios

- **001_api_response_normalization.php**: Normalize inconsistent API responses
- **002_config_bootstrap.php**: Load and merge configurations, feature flags
- **003_data_validation_pipeline.php**: Validate complex data structures

**Concepts**: API integration, config management, validation

## Key Features Demonstrated

### Parsing & Creation
- Parse JSON strings
- Create from arrays
- Load from files
- Create from objects

### Data Access
- Dot notation (user.profile.name)
- JSON Pointer (/user/profile/name)
- JSONPath queries ($.users[?(@.age > 18)])
- Magic properties ($json->name)
- Array access ($json['name'])

### Data Manipulation
- Set/update values
- Remove fields
- Merge documents
- Transform structures

### Iteration & Counting
- foreach loops
- Array functions (map, filter)
- Countable interface
- Nested iteration

### Type System
- Type coercion
- Type checking
- Safe conversions
- Enum usage

### Advanced Features
- Streaming large files
- Lazy loading with proxies
- Event system (PSR-14)
- Caching strategies
- Merge/diff operations

### Real-World Patterns
- API response normalization
- Configuration bootstrap
- Data validation
- Error handling
- Batch processing

## Shared Utilities (bootstrap.php)

The `bootstrap.php` file provides:

### Autoloading
- Composer PSR-4 autoloader
- Convenient use statements

### Data Generators
- `create_sample_user()` - Generate user data
- `create_sample_product()` - Generate product data
- `create_sample_transaction()` - Generate transaction data
- `create_sample_config()` - Generate configuration
- `create_sample_nested_data()` - Generate nested structures
- `create_sample_api_response()` - Generate API responses

### Output Helpers
- `print_section($title, $content)` - Print formatted sections
- `print_json($data, $title)` - Pretty-print JSON
- `get_data_dir()` - Get examples/data directory
- `get_example_file($filename)` - Get path to sample data file

## Sample Data

The `examples/data/` directory includes:

- **users.json**: Array of user objects with metadata
- **products.json**: Store structure with product catalog
- **config.json**: Application configuration
- **items.ndjson**: Newline-delimited JSON for streaming

These files allow examples to work with realistic data without external APIs.

## Usage Patterns

### Pattern 1: Basic Read/Write
```php
$json = Json::parse('{"name": "test"}');
echo $json->get('name');
$json->set('updated', true);
```

### Pattern 2: Fluent Building
```php
$json = Json::create()
    ->set('user.name', 'Alice')
    ->set('user.email', 'alice@example.com');
```

### Pattern 3: Querying
```php
$users = $json->query('$.users[?(@.active == true)]');
$user = $json->getPointer('/users/0/name');
```

### Pattern 4: Merging
```php
$json->mergeWithStrategy($other, DiffMergeStrategy::MERGE_RECURSIVE);
```

### Pattern 5: Events
```php
$json->getDispatcher()->subscribe(EventType::BEFORE_MUTATE->value, $callback);
```

## Architecture Notes

### Type Declarations
All examples use `declare(strict_types=1)` to enforce type safety.

### Error Handling
Examples demonstrate both successful operations and error scenarios with appropriate exception handling.

### Performance Considerations
Examples include:
- Streaming for large files
- Lazy loading for deferred operations
- Caching strategies for optimization

### Code Style
- Follows PSR-12 coding standards
- Uses modern PHP 8+ features
- Includes type hints where helpful
- Clear, readable variable names

## Learning Path

### For Beginners
1. Start with `01-basic-usage/001_parse_and_access.php`
2. Move to `02-fluent-interface/001_method_chaining.php`
3. Explore `04-array-access/001_array_access_interface.php`
4. Try `07-json-pointer-path/001_json_pointer.php`

### For Intermediate Developers
1. `08-streaming/001_streaming_large_files.php`
2. `09-lazy-loading/001_lazy_proxies.php`
3. `11-events/001_event_listeners.php`
4. `12-merge-strategies/001_merge_operations.php`

### For Advanced Usage
1. `13-real-world/001_api_response_normalization.php`
2. `13-real-world/002_config_bootstrap.php`
3. `13-real-world/003_data_validation_pipeline.php`

## Troubleshooting

### Example won't run
- Ensure PHP 8.0+ is installed: `php -v`
- Run `composer install` from project root
- Check file permissions: `chmod +x examples/*/`

### Autoloader errors
- Verify `bootstrap.php` is being included: `require_once dirname(__DIR__) . '/bootstrap.php';`
- Check that Composer's autoloader exists: `vendor/autoload.php`

### Memory issues with large data
- Consider streaming examples for large files
- Use lazy loading with `LazyJsonProxy`
- Implement caching strategies

## Further Reading

- [Main README](../README.md) - Full library documentation
- [API Documentation](../docs/) - Detailed API reference
- [JSON Standard (RFC 7158)](https://tools.ietf.org/html/rfc7158)
- [JSON Pointer (RFC 6902)](https://tools.ietf.org/html/rfc6902)
- [JSON Merge Patch (RFC 7396)](https://tools.ietf.org/html/rfc7396)
- [JSONPath Specification](https://goessner.net/articles/JsonPath/)

## Contributing

To add new examples:

1. Create a new PHP file in the appropriate directory
2. Follow the naming convention: `NNN_descriptive_name.php`
3. Include a docblock and clear output
4. Use the shared bootstrap utilities
5. Test with `php examples/folder/file.php`
6. Update this README if creating a new category

## License

These examples are part of the JSON Parser library and are licensed under the MIT License.

---

**Last Updated**: 2025-05-28  
**Version**: 1.0.0  
**Total Examples**: 60+
