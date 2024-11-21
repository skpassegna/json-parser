# PHP JSON Management Library

[![Latest Version on Packagist](https://img.shields.io/packagist/v/skpassegna/json-parser.svg?style=flat-square)](https://packagist.org/packages/skpassegna/json-parser)
[![Total Downloads](https://img.shields.io/packagist/dt/skpassegna/json-parser.svg?style=flat-square)](https://packagist.org/packages/skpassegna/json-parser)
[![Tests](https://github.com/skpassegna/json-parser/actions/workflows/tests.yml/badge.svg)](https://github.com/skpassegna/json-parser/actions/workflows/tests.yml)
[![PHP Version](https://img.shields.io/packagist/php-v/skpassegna/json-parser.svg?style=flat-square)](https://packagist.org/packages/skpassegna/json-parser)
[![License](https://img.shields.io/packagist/l/skpassegna/json-parser.svg?style=flat-square)](https://packagist.org/packages/skpassegna/json-parser)

A powerful, intuitive, and comprehensive JSON management library for PHP 8+. This library provides a fluent interface for working with JSON data, offering extensive functionality for parsing, manipulating, validating, and transforming JSON.

## Features

- 🚀 **Modern PHP 8+ Support**: Leverages latest PHP features including typed properties, union types, and attributes
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

## Requirements

- PHP 8.0 or higher
- ext-json
- ext-mbstring

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

### Streaming Large Files

```php
use Skpassegna\Json\Stream\JsonReader;
use Skpassegna\Json\Stream\JsonWriter;

// Read large JSON file
$reader = new JsonReader('large-file.json');
foreach ($reader as $item) {
    // Process each item
}

// Write large JSON file
$writer = new JsonWriter('output.json');
$writer->write($data);
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
