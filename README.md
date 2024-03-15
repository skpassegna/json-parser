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