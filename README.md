NOTE: This project is still under development.

### Implementing right now:

Here are the major updates to the `skpassegna/json-parser` library to support any kind of JSON operation, handle complex nested JSON data, and make it more intelligent, robust, and all-purpose when it comes to JSON:

1. **Supporting Any Kind of JSON Operation**:
  
  - **Validating JSON**: Add a new method `JsonParser::isValid($jsonString)` that checks if a given JSON string is valid or not. It should return a boolean value indicating whether the JSON is valid or not.
    
  - **Fixing JSON**: Add a new method `JsonParser::fix($jsonString)` that attempts to fix an invalid JSON string by correcting common syntax errors, removing invalid characters, and ensuring proper formatting. It should return a valid JSON string or throw an exception if the JSON cannot be fixed.
    
  - **Merging JSON**: Add a new method `JsonObject::merge($otherObject)` and `JsonArray::merge($otherArray)` that merges two JSON objects or arrays into one. For objects, it should merge the properties, and for arrays, it should concatenate the elements.
    
  - **Comparing JSON**: Add a new method `JsonObject::equals($otherObject)` and `JsonArray::equals($otherArray)` that compares two JSON objects or arrays for equality.
    
  - **Transforming JSON**: Add a new method `JsonObject::transform($callback)` and `JsonArray::transform($callback)` that applies a given callback function to each value in the JSON object or array, allowing for advanced transformations and manipulations.
    
2. **Parsing Complex Nested JSON**:
  
  - **Updating `JsonParser::parse()`**: Modify the `JsonParser::parse()` method to recursively parse nested JSON objects and arrays. Instead of just creating a `JsonObject` or `JsonArray` instance, it should create instances of `JsonObject` and `JsonArray` for nested structures as well.
    
  - **Updating `JsonHelper::has()` and `JsonHelper::get()`**: Update the `JsonHelper::has()` and `JsonHelper::get()` methods to handle nested JSON structures more efficiently. Instead of relying on string manipulation and recursion, they should use a more robust approach to traverse the nested data structure.
    
3. **Intelligent, Robust, and All-Purpose JSON Handling**:
  
  - **Error Handling**: Enhance error handling by introducing more specific exceptions for different types of errors (e.g., `InvalidJsonException`, `MalformedJsonException`, `JsonParseException`, etc.). These exceptions should provide detailed information about the error, including the line and column number where the error occurred.
    
  - **JSON Schema Validation**: Implement support for JSON Schema validation by adding a new class `JsonSchemaValidator`. This class should allow users to validate JSON data against a given JSON Schema to ensure that the data conforms to a predefined structure and rules.
    
  - **JSON Path Support**: Introduce support for JSON Path expressions by adding a new class `JsonPath`. This class should provide methods to query JSON data using JSON Path expressions, allowing for advanced data retrieval and manipulation.
    
  - **JSON Diff and Patch**: Implement JSON diff and patch functionality by adding new classes `JsonDiff` and `JsonPatch`. The `JsonDiff` class should calculate the differences between two JSON documents, while the `JsonPatch` class should apply JSON Patch operations to modify JSON data.
    
  - **JSON Streaming**: Add support for streaming large JSON data by introducing a new class `JsonStream`. This class should allow users to process JSON data in a streaming fashion, reducing memory usage and enabling efficient handling of large JSON datasets.
    
  - **JSON Compression**: Implement JSON compression and decompression functionality by adding new methods `JsonObject::compress()` and `JsonObject::decompress()`. These methods should allow users to compress and decompress JSON data, reducing the size of the data for efficient storage and transmission.
    
  - **JSON Encryption and Decryption**: Introduce JSON encryption and decryption functionality by adding new methods `JsonObject::encrypt($key)` and `JsonObject::decrypt($key)`. These methods should allow users to encrypt and decrypt JSON data using a given encryption key, ensuring data security during transmission or storage.
    
  - **JSON Formatting and Minification**: Add support for JSON formatting and minification by introducing new methods `JsonObject::format()` and `JsonObject::minify()`. The `format()` method should pretty-print JSON data with proper indentation and spacing, while the `minify()` method should remove unnecessary whitespace and formatting to reduce the size of the JSON data.
    
  - **JSON Serialization and Deserialization**: Enhance the library's serialization and deserialization capabilities by adding support for custom JSON serializers and deserializers. Users should be able to register custom handlers for specific data types, allowing for more flexible and extensible JSON serialization and deserialization.
    
  - **JSON Performance Optimizations**: Optimize the library's performance by introducing caching mechanisms, lazy loading, and other performance-enhancing techniques. This will ensure that the library can handle large JSON datasets efficiently and provide optimal performance in various use cases.
    
  - **JSON Documentation and Examples**: Improve the library's documentation by providing comprehensive examples and use cases for each feature and functionality. Additionally, create interactive demos and tutorials to help users understand and effectively utilize the library's capabilities.
    

By implementing these updates, the `skpassegna/json-parser` library will become a truly comprehensive, intelligent, and robust solution for handling any kind of JSON-related operation. It will be capable of parsing, validating, transforming, merging, comparing, and performing advanced operations on JSON data, including nested structures, while providing strong error handling, schema validation, and performance optimizations.

# JSON Parser

Welcome to the JSON Parser Wiki! This wiki provides detailed documentation and examples for using the JSON Parser library.

## Table of Contents

1. [Introduction](#introduction)
2. [Installation](#installation)
3. [Parsing JSON](#parsing-json)
4. [Working with JSON Objects](#working-with-json-objects)
  - [Accessing Properties](#accessing-properties)
  - [Modifying Properties](#modifying-properties)
  - [Iterating Over Properties](#iterating-over-properties)
  - [Converting to Array and JSON String](#converting-to-array-and-json-string)
5. [Working with JSON Arrays](#working-with-json-arrays)
  - [Filtering Elements](#filtering-elements)
  - [Mapping Elements](#mapping-elements)
  - [Sorting Elements](#sorting-elements)
  - [Getting the Number of Elements](#getting-the-number-of-elements)
6. [Accessing Nested Values](#accessing-nested-values)
7. [Error Handling](#error-handling)
  - [JSON Parsing Errors](#json-parsing-errors)
  - [Key Not Found Exceptions](#key-not-found-exceptions)
8. [Testing](#testing)
9. [Contributing](#contributing)
10. [License](#license)

## Introduction

The JSON Parser library is a robust and user-friendly JSON parsing library for PHP. It provides a convenient way to work with JSON data, allowing you to parse, access, modify, and iterate over JSON objects and arrays with ease.

## Installation

You can install the library via Composer:

```bash
composer require skpassegna/json-parser
```

## Parsing JSON

To parse a JSON string, use the `JsonParser` class:

```php
use Skpassegna\JsonParser\JsonParser;

$jsonString = '{"name":"John Doe","age":30,"address":{"street":"123 Main St","city":"Anytown","state":"CA"}}';
$parser = new JsonParser();
$jsonObject = $parser->parse($jsonString);
```

The `parse` method returns either a `JsonObject` or a `JsonArray` instance, depending on the structure of the JSON data.

## Working with JSON Objects

The `JsonObject` class provides methods for accessing and modifying JSON object properties.

### Accessing Properties

You can access JSON object properties using the `get` method or directly using object syntax:

```php
// Using the get method
$name = $jsonObject->get('name'); // "John Doe"

// Using object syntax
$name = $jsonObject->name; // "John Doe"
```

The `get` method also supports accessing nested values using dot notation:

```php
$street = $jsonObject->get('address.street'); // "123 Main St"
```

### Modifying Properties

You can set or remove JSON object properties using the `set` and `remove` methods, respectively:

```php
// Set a property value
$jsonObject->set('name', 'Jane Doe');

// Remove a property
$jsonObject->remove('age');
```

You can also set properties directly using object syntax:

```php
$jsonObject->name = 'Jane Doe';
```

### Iterating Over Properties

The `JsonObject` class implements the `IteratorAggregate` interface, allowing you to iterate over its properties:

```php
foreach ($jsonObject as $key => $value) {
    echo "$key: $value\n";
}
```

### Converting to Array and JSON String

You can convert a `JsonObject` instance to a PHP array or a JSON string using the `toArray` and `toJson` methods, respectively:

```php
// Convert to an array
$data = $jsonObject->toArray();

// Convert to a JSON string
$jsonString = $jsonObject->toJson();
```

## Working with JSON Arrays

The `JsonArray` class extends `JsonObject` and provides additional methods for working with JSON arrays.

### Filtering Elements

You can filter the elements of a JSON array using the `filter` method, which accepts a callback function:

```php
$jsonArray = new JsonArray([1, 2, 3, 4, 5]);

// Filter even numbers
$evenNumbers = $jsonArray->filter(function ($value) {
    return $value % 2 === 0;
});
```

### Mapping Elements

You can map the elements of a JSON array to a new array using the `map` method, which accepts a callback function:

```php
$jsonArray = new JsonArray([1, 2, 3, 4, 5]);

// Double each number
$doubledNumbers = $jsonArray->map(function ($value) {
    return $value * 2;
});
```

### Sorting Elements

You can sort the elements of a JSON array using the `sort` method:

```php
$jsonArray = new JsonArray([5, 3, 1, 4, 2]);

// Sort in ascending order
$sortedNumbers = $jsonArray->sort();

// Sort in descending order using a custom comparison function
$sortedNumbers = $jsonArray->sort(function ($a, $b) {
    return $b <=> $a;
});
```

### Getting the Number of Elements

You can get the number of elements in a JSON array using the `count` method:

```php
$jsonArray = new JsonArray([1, 2, 3, 4, 5]);

$count = $jsonArray->count(); // 5
```

## Accessing Nested Values

You can access nested values in JSON objects and arrays using dot notation with the `get` and `has` methods:

```php
$street = $jsonObject->get('address.street'); // "123 Main St"
$hasCity = $jsonObject->has('address.city'); // true

$city = $jsonArray->get('2.city'); // "Anytown"
$hasState = $jsonArray->has('2.state'); // true
```

Alternatively, you can use the `JsonHelper` class, which provides static methods for accessing nested values in both `JsonObject` and `JsonArray` instances:

```php
use Skpassegna\JsonParser\JsonHelper;

$street = JsonHelper::get($jsonObject, 'address.street'); // "123 Main St"
$hasCity = JsonHelper::has($jsonObject, 'address.city'); // true

$city = JsonHelper::get($jsonArray, '2.city'); // "Anytown"
$hasState = JsonHelper::has($jsonArray, '2.state'); // true
```

## Error Handling

The library includes two types of exceptions for handling errors: `HumanReadableJsonException` and `JsonKeyNotFoundException`.

### JSON Parsing Errors

The `HumanReadableJsonException` class is thrown when there is an error parsing a JSON string. This exception provides a human-readable error message based on the JSON error code, making it easier to diagnose and resolve issues:

```php
use Skpassegna\JsonParser\Exceptions\HumanReadableJsonException;

try {
    $jsonObject = $parser->parse('{invalid}');
} catch (HumanReadableJsonException $e) {
    echo $e->getMessage(); // "Invalid or malformed JSON data. The syntax is incorrect."
}
```

### Key Not Found Exceptions

The `JsonKeyNotFoundException` is thrown when attempting to access or modify non-existent keys or nested values in `JsonObject` and `JsonArray` instances:

```php
use Skpassegna\JsonParser\Exceptions\JsonKeyNotFoundException;

try {
    $value = $jsonObject->get('nonexistent');
} catch (JsonKeyNotFoundException $e) {
    echo $e->getMessage(); // "Key 'nonexistent' not found in JSON data."
}
```

## Testing

The library includes a comprehensive suite of unit tests to ensure its functionality and reliability. You can run the tests using PHPUnit:

```bash
./vendor/bin/phpunit
```

## Contributing

Contributions to the JSON Parser library are welcome! If you find any issues or have suggestions for improvements, please open an issue or submit a pull request on the [GitHub repository](https://github.com/skpassegna/json-parser).

## License

The JSON Parser library is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).