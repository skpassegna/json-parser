# JSON Parser & Manager

A comprehensive, fluent, and feature-rich library for managing, parsing, transforming, and streaming JSON in PHP.

## Features

* **Fluent Interface**: Intuitive chaining methods for JSON manipulation.
* **Access Methods**: Dot-notation access (`get`, `set`, `has`, `remove`).
* **Querying**: Full support for JSONPath and JSON Pointer (RFC 6902).
* **Streaming**: Memory-efficient parsing and generation for large files (PSR-7 compatible), including NDJSON.
* **Lazy Loading**: Defer parsing until data is strictly needed via proxies.
* **Transformation**: Convert between JSON and XML, YAML, CSV, HTML, and JSON5.
* **Advanced Merge/Diff**: Strategies for deep merging, recursive merging, and conflict detection.
* **Validation**: Schema-based validation using `justinrainbow/json-schema` and structure inference.
* **Events**: PSR-14 compatible event system for hooking into parsing, merging, and diffing lifecycles.
* **Reflection**: Deep inspection of JSON structure, statistics, and schema inference.

## Requirements

* PHP ^8.0 || ^8.1 || ^8.2 || ^8.3 || ^8.4
* `ext-json`
* `ext-mbstring`

## Installation

```bash
composer require skpassegna/json-parser
```

## Usage

### Basic Manipulation

Initialize and manipulate data using the fluent `Json` class.

```php
use Skpassegna\Json\Json;

// Parse
$json = Json::parse('{"user": {"name": "John", "age": 30}}');

// Get (Dot Notation)
$name = $json->get('user.name'); 

// Set
$json->set('user.role', 'admin');

// JSON Pointer
$age = $json->getPointer('/user/age');

// Convert back to string
echo $json->toString(); 
```

### JSONPath Querying

Query complex structures using standard JSONPath syntax.

```php
$json = Json::parse($storeData);
$books = $json->query('$.store.book[?(@.price < 10)]');
```

### Streaming (Large Files)

Process large JSON files or streams without loading the entire file into memory.

```php
use Skpassegna\Json\Json;

// Parse Stream
foreach (Json::parseStream($psr7Stream) as $chunk) {
    // Process chunk
}

// Streaming Builder configuration
Json::streaming()
    ->withChunkSize(8192)
    ->onChunk(fn($chunk, $bytes) => logger("Read $bytes bytes"))
    ->parse($psr7Stream);
```

### Transformation

Convert JSON data to various formats seamlessly.

```php
// To XML
$xml = $json->toXml('root');

// To CSV
$csv = $json->toCsv();

// Import from HTML
$json = Json::create()->fromHtml('<div><span>Hello</span></div>');

// Import from JSON5 (supports comments)
$json = Json::create()->fromJson5("{ key: 'value' // comment }");
```

### Advanced Merging and Diffing

Utilize specific strategies for combining or comparing documents.

```php
use Skpassegna\Json\Enums\DiffMergeStrategy;

// Merge
$json->mergeWithStrategy(
    ['config' => 'new'], 
    DiffMergeStrategy::MERGE_RECURSIVE
);

// Diff
$diff = $json->diffWithStrategy(
    $otherJson, 
    DiffMergeStrategy::DIFF_DETAILED
);
```

### Event System

Hook into lifecycle events using the internal dispatcher.

```php
use Skpassegna\Json\Enums\EventType;

$json->getDispatcher()->subscribe(EventType::BEFORE_PARSE->value, function($event) {
    // Log parsing start
});
```

### Schema Validation & Generation

```php
use Skpassegna\Json\Json\SchemaGenerator;

// Validate against array schema
$isValid = $json->validateSchema([
    'type' => 'object', 
    'required' => ['user']
]);

// Generate Schema from PHP Class
$generator = new SchemaGenerator();
$schema = $generator->generate(UserDTO::class);
```

## Procedural Helpers

The package includes helper functions for functional programming styles.

* `json_parse(string $json): Json`
* `json_get(Json|array $data, string $path): mixed`
* `json_set(Json $data, string $path, mixed $value): Json`
* `json_query(Json|array $data, string $path): array`
* `json_to_xml(Json|array $data): string`
* `json_to_csv(Json|array $data): string`

## License

This project is licensed under the MIT License - see the LICENSE file for details.