# JSON Parser Documentation

## Introduction

The JSON Parser library is a robust and user-friendly solution for parsing JSON data into objects in PHP. It follows the SOLID principles and includes comprehensive error handling mechanisms to ensure reliable and consistent behavior.

This documentation will guide you through the installation process, provide detailed usage examples, cover advanced use cases, and explain the library's architecture and design decisions.

## Installation

You can install the JSON Parser library via Composer:

```bash
composer require skpassegna/json-parser
```

After installing the library, you can import the necessary classes into your PHP files:

```php
use Skpassegna\JsonParser\JsonParser;
use Skpassegna\JsonParser\JsonObject;
use Skpassegna\JsonParser\JsonArray;
use Skpassegna\JsonParser\JsonValue;
use Skpassegna\JsonParser\Exceptions\HumanReadableJsonException;
use Skpassegna\JsonParser\JsonHelper;
use Skpassegna\JsonParser\Contracts\JsonAccessible;
use Skpassegna\JsonParser\Contracts\JsonIterable;
```

## Basic Usage

### Parsing JSON

To parse a JSON string, you can use the `JsonParser` class:

```php
$jsonString = '{"name": "John Doe", "age": 30, "email": "john@example.com"}';
$parser = new JsonParser();
$jsonObject = $parser->parse($jsonString);
```

The `parse` method returns a `JsonObject` instance, which represents the parsed JSON data.

### Accessing Properties

You can access the properties of a `JsonObject` using the object syntax, the `get` method, or the array access syntax:

```php
echo $jsonObject->name; // Output: John Doe
echo $jsonObject->get('age'); // Output: 30
echo $jsonObject['email']; // Output: john@example.com
```

### Nested Properties

The library supports accessing nested properties using dot notation:

```php
echo $jsonObject->get('address.city'); // Access a nested property
```

### Working with Arrays

If the JSON data contains an array, the `parse` method will return a `JsonArray` instance, which extends the `JsonObject` class and provides additional array-specific methods:

```php
$jsonArray = $parser->parse('[1, 2, 3, 4, 5]');
echo $jsonArray->count(); // Output: 5

$filtered = $jsonArray->filter(function ($value) {
    return $value % 2 === 0;
});

echo $filtered->toJson(); // Output: [2, 4]
```

You can also access array elements using the array access syntax:

```php
echo $jsonArray[0]; // Output: 1
```

### Converting to Array or JSON

You can convert a `JsonObject` or `JsonArray` instance back to a PHP array or JSON string using the `toArray` and `toJson` methods, respectively:

```php
$array = $jsonObject->toArray();
$jsonString = $jsonObject->toJson();
```

### Error Handling

If an invalid JSON string is provided, the `parse` method will throw a `HumanReadableJsonException` with a descriptive error message:

```php
try {
    $parser->parse('invalid json');
} catch (HumanReadableJsonException $e) {
    echo $e->getMessage(); // Output: Invalid or malformed JSON data. The syntax is incorrect.
}
```

### Helper Functions

The library includes a `JsonHelper` class that provides static helper methods for working with JSON data:

```php
$data = $parser->parse('{"person": {"name": "John Doe", "age": 30}}');

if (JsonHelper::has($data, 'person.name')) {
    echo JsonHelper::get($data, 'person.name'); // Output: John Doe
}
```

The `has` method checks if a value exists at a given key path, while the `get` method retrieves the value at the specified key path.

## Advanced Usage

### Setting and Removing Properties

You can set and remove properties in a `JsonObject` using the `set` and `remove` methods, respectively:

```php
$jsonObject = new JsonObject();
$jsonObject->set('name', 'John Doe');
$jsonObject->set('age', 30);

echo $jsonObject->name; // Output: John Doe
echo $jsonObject->age; // Output: 30

$jsonObject->remove('age');
echo $jsonObject->has('age'); // Output: false
```

You can also use the array access syntax to set and remove properties:

```php
$jsonObject['email'] = 'john@example.com';
unset($jsonObject['email']);
```

### Working with Nested Properties

The `JsonObject` class provides methods for working with nested properties using dot notation:

```php
$jsonObject = new JsonObject([
    'person' => [
        'name' => 'John Doe',
        'age' => 30,
        'address' => [
            'city' => 'New York',
            'country' => 'USA',
        ],
    ],
]);

echo $jsonObject->get('person.name'); // Output: John Doe
echo $jsonObject->get('person.address.city'); // Output: New York

$jsonObject->set('person.email', 'john@example.com');
echo $jsonObject->get('person.email'); // Output: john@example.com

$jsonObject->remove('person.address.country');
echo $jsonObject->has('person.address.country'); // Output: false
```

### Array Methods

The `JsonArray` class extends the `JsonObject` class and provides additional array-specific methods for filtering, mapping, and sorting:

```php
$jsonArray = new JsonArray([1, 2, 3, 4, 5]);

// Filter
$filtered = $jsonArray->filter(function ($value) {
    return $value % 2 === 0;
});
echo $filtered->toJson(); // Output: [2, 4]

// Map
$mapped = $jsonArray->map(function ($value) {
    return $value * 2;
});
echo $mapped->toJson(); // Output: [2, 4, 6, 8, 10]

// Sort
$sorted = $jsonArray->sort();
echo $sorted->toJson(); // Output: [1, 2, 3, 4, 5]
```

### JsonValue

The `JsonValue` class represents a JSON value, which can be a string, number, boolean, or null. It provides methods for checking the type of the value and converting it to a string representation:

```php
$jsonValue = new JsonValue('Hello, World!');
echo $jsonValue->isString(); // Output: true
echo (string)$jsonValue; // Output: Hello, World!

$jsonValue = new JsonValue(42);
echo $jsonValue->isNumber(); // Output: true
echo $jsonValue->isInteger(); // Output: true
echo $jsonValue->getValue(); // Output: 42

$jsonValue = new JsonValue(true);
echo $jsonValue->isBoolean(); // Output: true
echo (string)$jsonValue; // Output: 1

$jsonValue = new JsonValue(null);
echo $jsonValue->isNull(); // Output: true
echo (string)$jsonValue; // Output: (empty string)
```

### Using Interfaces

The library adheres to the Interface Segregation Principle (ISP) by providing separate interfaces for different client types. The `JsonObject` and `JsonArray` classes implement the `JsonAccessible` and `JsonIterable` interfaces, allowing array-like access and iteration over the object properties.

```php
use Skpassegna\JsonParser\JsonParser;
use Skpassegna\JsonParser\Contracts\JsonAccessible;
use Skpassegna\JsonParser\Contracts\JsonIterable;

$jsonString = '{"name": "John Doe", "age": 30, "email": "john@example.com"}';
$parser = new JsonParser();
$jsonObject = $parser->parse($jsonString);

// Using JsonAccessible
if ($jsonObject instanceof JsonAccessible) {
    echo $jsonObject['name']; // Output: John Doe
    $jsonObject['phone'] = '555-1234';
}

// Using JsonIterable
if ($jsonObject instanceof JsonIterable) {
    foreach ($jsonObject as $key => $value) {
        echo "$key: $value\n";
    }
}
```

The `JsonAccessible` interface provides methods for array-like access, while the `JsonIterable` interface extends the built-in `IteratorAggregate` interface and allows iteration over the object properties.

## Architecture and Design Decisions

The JSON Parser library follows the SOLID principles and the DRY (Don't Repeat Yourself) principle to promote code reusability, testability, and maintainability.

### Single Responsibility Principle (SRP)

The library is designed with multiple classes, each responsible for a specific task:

- `JsonParser`: Responsible for parsing JSON strings into instances of `JsonObject`, `JsonArray`, or `JsonValue`.
- `JsonObject`: Represents a JSON object and provides methods for accessing and manipulating its properties.
- `JsonArray`: Extends `JsonObject` and represents a JSON array, providing additional array-specific methods.
- `JsonValue`: Represents a JSON value (string, number, boolean, or null) and provides methods for working with the value.
- `HumanReadableJsonException`: Represents exceptions that occur during JSON parsing or object manipulation and provides descriptive error messages.
- `JsonHelper`: Provides helper functions for common operations, such as checking if a value exists in an object or array, and retrieving values by key path.

### Open-Closed Principle (OCP)

The library is designed to be open for extension but closed for modification. It uses interfaces and abstract classes to promote code extensibility and adherence to the Open-Closed Principle (OCP) and Liskov Substitution Principle (LSP).

### Dependency Inversion Principle (DIP)

The library follows the Dependency Inversion Principle by relying on abstractions (interfaces and abstract classes) rather than concrete implementations. This promotes loose coupling and makes the code more testable and maintainable.

### Interface Segregation Principle (ISP)

The library adheres to the Interface Segregation Principle by providing separate interfaces for different client types. The `JsonObject` and `JsonArray` classes implement the `JsonAccessible` and `JsonIterable` interfaces, allowing array-like access and iteration over the object properties.

### Don't Repeat Yourself (DRY)

The library follows the DRY principle by using helper functions and abstract classes to avoid code duplication. For example, the `JsonHelper` class provides helper functions for common operations, and the `JsonObject` class serves as a base class for both `JsonObject` and `JsonArray`.

## Testing

The JSON Parser library includes comprehensive test cases to ensure its correctness and reliability. The tests are located in the `tests` directory and can be run using PHPUnit:

```bash
vendor/bin/phpunit
```

The test cases cover various scenarios, including parsing valid and invalid JSON strings, accessing and manipulating properties, working with nested data, testing the helper functions, and ensuring the correct implementation of the `JsonAccessible` and `JsonIterable` interfaces.

## Contributing

Contributions to the JSON Parser library are welcome! If you encounter any issues or have suggestions for improvements, please open an issue or submit a pull request on the project's GitHub repository: https://github.com/skpassegna/json-parser

When contributing, please follow the coding standards and guidelines outlined in the project's documentation.

## License

The JSON Parser library is open-source and released under the [MIT License](LICENSE).