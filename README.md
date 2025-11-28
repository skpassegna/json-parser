# PHP JSON Management Library

[![Latest Version on Packagist](https://img.shields.io/packagist/v/skpassegna/json-parser.svg?style=flat-square)](https://packagist.org/packages/skpassegna/json-parser)
[![Total Downloads](https://img.shields.io/packagist/dt/skpassegna/json-parser.svg?style=flat-square)](https://packagist.org/packages/skpassegna/json-parser)

[![codecov](https://codecov.io/gh/skpassegna/json-parser/graph/badge.svg?token=NSLENRDVQ1)](https://codecov.io/gh/skpassegna/json-parser)

[![PHP Version](https://img.shields.io/packagist/php-v/skpassegna/json-parser.svg?style=flat-square)](https://packagist.org/packages/skpassegna/json-parser)
[![License](https://img.shields.io/packagist/l/skpassegna/json-parser.svg?style=flat-square)](https://packagist.org/packages/skpassegna/json-parser)

A powerful, intuitive, and comprehensive JSON management library for PHP 8+. This library provides a fluent interface for working with JSON data, offering extensive functionality for parsing, manipulating, validating, and transforming JSON.

## Features

- 🚀 **Modern PHP 8.0-8.4 Support**: Leverages latest PHP features including typed properties, union types, readonly properties, backed enums, and first-class callables
- 🛡️ **Type-Safe Operations**: Strong type checking and validation for reliable JSON handling
- 🔄 **Fluent Interface**: Chainable methods for intuitive JSON manipulation
- 📝 **JSON Schema Validation**: Built-in support for JSON Schema validation
- 🎯 **Path Operations**: JSONPath and Pointer support for precise data access
- 🔍 **Advanced Querying**: Complex data querying capabilities
- 🛠️ **Data Transformation**: Rich set of transformation and mapping functions
- 🔒 **Security Features**: Protection against JSON vulnerabilities
- 📊 **Format Conversion**: Convert between JSON and various formats (XML, YAML, CSV)
- 🎨 **Pretty Printing**: Customizable JSON formatting options
- 🔄 **Streaming Support**: Efficient handling of large JSON files
- ⚡ **High Performance**: Optimized for speed and memory efficiency
- 🧪 **Extensive Testing**: Comprehensive test coverage
- 📚 **Rich Documentation**: Detailed documentation with examples
- ✨ **PHP 8.4+ Array Helpers**: `array_find`, `array_find_key`, `array_any`, `array_all` with polyfills for earlier versions
- 🔀 **Type Coercion Service**: Strict/lenient type normalization with edge case handling
- 📋 **Backed Enums**: `JsonMergeStrategy`, `NumberFormat`, `TraversalMode` for type-safe constants
- 📡 **Streaming APIs**: PSR-7 stream support for large file processing with chunking
- 🔄 **Lazy Loading**: Deferred parsing with ArrayAccess and magic method support
- 💾 **Query Caching**: PSR-16-like cache interface with memory optimization
- 🎯 **NDJSON Support**: Native newline-delimited JSON parsing and serialization

## Requirements

- PHP 8.0+ (Supports PHP 8.0, 8.1, 8.2, 8.3, 8.4)
- ext-json
- ext-mbstring

### PHP 8.4+ Features

This library is built with PHP 8.4 readiness in mind. Features include:
- Backed enums for type-safe constants
- Array helper methods with built-in polyfills for PHP 8.0-8.3
- First-class callable support
- Full type union/intersection support

## Installation

Install the package via Composer:

```bash
composer require skpassegna/json-parser
```

## Basic Usage

```php
use Skpassegna\Json\Json;

// Parse JSON string
$json = Json::parse('{"name": "John", "age": 30}');

// Access data
$name = $json->get('name'); // "John"

// Modify data
$json->set('age', 31)
     ->set('city', 'New York');

// Add nested data
$json->set('address', [
    'street' => '123 Main St',
    'city' => 'New York',
    'country' => 'USA'
]);

// Convert back to JSON string
$jsonString = $json->toString();

// Pretty print
$prettyJson = $json->toString(Json::PRETTY_PRINT);

// Validate against schema
$isValid = $json->validateSchema($schema);

// Query using JSONPath
$results = $json->query('$.address.city');
```

## Detailed Usage

```php
use Skpassegna\Json\Json;

// Parse JSON string
$json = Json::parse('{"name": "John", "age": 30}');

// Access data
$name = $json->get('name'); // "John"
$age = $json->get('age'); // 30

// Modify data
$json->set('age', 31);

// Convert back to JSON
$jsonString = $json->toJson();
```

## Advanced Features

### JSON Schema Validation

```php
use Skpassegna\Json\Schema\Validator;

$schema = [
    'type' => 'object',
    'properties' => [
        'name' => ['type' => 'string'],
        'age' => ['type' => 'integer']
    ]
];

$json->validateSchema($schema); // Returns true/false
$json->validateSchemaWithErrors($schema); // Returns validation errors
```

### Data Transformation

```php
// Transform JSON to XML
$xml = $json->toXml();

// Transform JSON to YAML
$yaml = $json->toYaml();

// Transform JSON to CSV
$csv = $json->toCsv();
```

### PHP 8.4+ Array Helpers and Type Coercion

```php
// Find first element matching condition (PHP 8.4+)
$users = new Json([
    ['id' => 1, 'name' => 'Alice', 'age' => 25],
    ['id' => 2, 'name' => 'Bob', 'age' => 30],
]);

$adult = $users->findElement(fn($user) => $user['age'] >= 30);
$hasYoung = $users->anyMatch(fn($user) => $user['age'] < 18);
$allAdults = $users->allMatch(fn($user) => $user['age'] >= 18);

// Type coercion with strict/lenient modes
$json->enableStrictCoercion(false);
$int = $json->coerceInt('42');        // 42
$bool = $json->coerceBool('true');    // true
```

### Streaming Large Files with PSR-7 Support

```php
// Parse large JSON from PSR-7 stream
use Psr\Http\Message\StreamInterface;

foreach (Json::parseStream($stream, chunkSize: 8192) as $item) {
    // Process each item without loading entire file into memory
}

// Parse newline-delimited JSON (NDJSON)
foreach (Json::parseNdJsonStream($stream, chunkSize: 8192) as $record) {
    // Process each line as separate JSON object
}

// Serialize data to stream
$items = [/* ... */];
foreach (Json::serializeNdJsonStream($items, $stream) as $chunk) {
    // Write NDJSON chunks
}
```

### Lazy Loading for Deferred Parsing

```php
// Create lazy proxy that defers decoding
$config = Json::lazy(function () {
    return json_decode(file_get_contents('config.json'), true);
}, prefetch: false);

// Data not loaded yet
echo $config['database']['host']; // Loads on first access

// Access via array syntax
$config['api']['timeout'] = 30;

// Iterate over lazy data
foreach ($config as $key => $value) {
    echo "$key: $value\n";
}
```

### Query Caching for Performance

```php
// Create cache store
$cache = Json::cache();

// Query with automatic caching
$results = $json->queryWithCache(
    '$.users[?(@.role=="admin")]',
    cache: $cache,
    ttl: 3600
);

// Subsequent queries use cached results
$results = $json->queryWithCache(
    '$.users[?(@.role=="admin")]',
    cache: $cache
);

// Build with fluent configuration
$builder = Json::streaming()
    ->withChunkSize(16384)
    ->withCache()
    ->withBufferLimit(52428800)
    ->ndJson();

foreach ($builder->parse($stream) as $item) {
    // Process NDJSON with caching enabled
}
```

### Security Features

```php
// Sanitize JSON input
$json = Json::parse($input, ['sanitize' => true]);

// Maximum depth protection
$json = Json::parse($input, ['max_depth' => 10]);

// Maximum length protection
$json = Json::parse($input, ['max_length' => 1000000]);
```

## Code Coverage

![Codecov](https://codecov.io/github/skpassegna/json-parser/graphs/sunburst.svg?token=NSLENRDVQ1)

The sunburst chart above provides a visual representation of code coverage across the project. The inner-most circle represents the entire project, with subsequent layers representing folders and files. The size and color of each slice indicate the number of statements and coverage percentage, respectively.

## Error Handling

The library uses custom exceptions for different types of errors:

- `JsonException`: Base exception for all JSON-related errors
- `ParseException`: JSON parsing errors
- `ValidationException`: Schema validation errors
- `PathException`: JSONPath related errors
- `TransformException`: Data transformation errors

```php
use Skpassegna\Json\Exceptions\JsonException;

try {
    $json = Json::parse($invalidJson);
} catch (JsonException $e) {
    echo $e->getMessage();
}
```

## Contributing

Contributions are welcome! Please see the [CONTRIBUTING.md](CONTRIBUTING.md) file for guidelines on how to contribute.

## FAQs and Troubleshooting

- **How do I handle errors?**
  Ensure you catch exceptions when parsing or manipulating JSON data.

- **What PHP versions are supported?**
  The library supports PHP 8.0 and higher.

## Changelog

For a list of changes and updates, please refer to the [CHANGELOG.md](CHANGELOG.md).

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Credits

- [All Contributors](../../contributors)

## Security

If you discover any security related issues, please email security@example.com instead of using the issue tracker.
