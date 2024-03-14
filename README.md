# Json Parser: A Beginner's Guide

`skpassegna/json-parser` is a powerful JSON parsing library for PHP that makes working with JSON data a breeze. Whether you're a beginner or an experienced developer, this library provides a simple and intuitive way to handle JSON data in your PHP applications.

## What is JSON?

JSON (JavaScript Object Notation) is a lightweight data-interchange format that is easy for humans to read and write, and easy for machines to parse and generate. It's widely used for transmitting data between a server and web applications, as well as for storing and retrieving data.

Here's an example of a JSON object:

```json
{
  "name": "John Doe",
  "age": 30,
  "isStudent": true,
  "hobbies": ["reading", "hiking", "coding"]
}
```

JSON data can be structured as objects (key-value pairs) or arrays (ordered lists).

## Installing the Library

To start using `skpassegna/json-parser`, you'll need to install it via Composer, the popular PHP package manager. Open your terminal or command prompt, navigate to your project directory, and run the following command:

```
composer require skpassegna/json-parser
```

Composer will automatically download and install the library and its dependencies.

## Parsing JSON Strings

The first step in working with JSON data is to parse a JSON string into a PHP object or array. With `skpassegna/json-parser`, you can do this using the `JsonParser` class:

```php
use Skpassegna\JsonParser\JsonParser;

$jsonParser = new JsonParser();
$data = $jsonParser->parse('{"name":"John Doe","age":30}');
```

The `parse` method returns either a `JsonObject` or a `JsonArray` instance, depending on the structure of the JSON data. These classes provide convenient methods for accessing and manipulating the parsed data.

## Working with JsonObject

The `JsonObject` class represents a JSON object and provides methods for accessing and manipulating its properties.

### Accessing Properties

You can access properties using object notation or the `get` method:

```php
echo $data->name; // Output: "John Doe"
echo $data->get('age'); // Output: 30
```

### Setting Properties

You can set properties using object notation or the `set` method:

```php
$data->name = "Jane Doe";
$data->set('age', 32);
```

### Checking Property Existence

You can check if a property exists using the `has` method:

```php
if ($data->has('name')) {
    // Property "name" exists
}
```

### Removing Properties

You can remove a property using the `remove` method:

```php
$data->remove('age');
```

### Converting to Array or JSON

You can convert a `JsonObject` instance to an associative array or a JSON string using the `toArray` or `toJson` methods, respectively:

```php
$array = $data->toArray();
$jsonString = $data->toJson();
```

### Iterating Over Properties

You can iterate over the properties of a `JsonObject` instance using a `foreach` loop or the `getIterator` method:

```php
foreach ($data as $key => $value) {
    echo "$key: $value\n";
}

$iterator = $data->getIterator();
while ($iterator->valid()) {
    $key = $iterator->key();
    $value = $iterator->current();
    echo "$key: $value\n";
    $iterator->next();
}
```

## Working with JsonArray

The `JsonArray` class represents a JSON array and provides methods for filtering, mapping, and sorting its elements.

### Filtering Elements

You can filter the elements of a `JsonArray` instance using the `filter` method:

```php
$filteredArray = $data->filter(function ($value) {
    return $value > 10;
});
```

### Mapping Elements

You can map the elements of a `JsonArray` instance to a new array using the `map` method:

```php
$mappedArray = $data->map(function ($value) {
    return $value * 2;
});
```

### Sorting Elements

You can sort the elements of a `JsonArray` instance using the `sort` method:

```php
$sortedArray = $data->sort(); // Sort in ascending order

// Sort using a custom comparison function
$sortedArray = $data->sort(function ($a, $b) {
    return $b - $a; // Sort in descending order
});
```

### Getting the Count

You can get the number of elements in a `JsonArray` instance using the `count` method:

```php
$count = $data->count();
```

## Working with Nested Data

The `JsonHelper` class provides utility methods for working with nested data in JSON objects and arrays.

### Checking for Nested Values

You can check if a nested value exists using the `has` method:

```php
use Skpassegna\JsonParser\JsonHelper;

$data = $jsonParser->parse('{"person":{"name":"John Doe","age":30}}');

if (JsonHelper::has($data, 'person.name')) {
    // The nested value "person.name" exists
}
```

### Getting Nested Values

You can retrieve a nested value using the `get` method:

```php
$name = JsonHelper::get($data, 'person.name'); // Output: "John Doe"
$age = JsonHelper::get($data, 'person.age', 0); // Output: 30
```

The third argument to `get` is an optional default value to return if the specified key path doesn't exist.

## Handling JSON Errors

If the `parse` method encounters an invalid JSON string, it will throw a `HumanReadableJsonException` with a human-readable error message based on the JSON error code.

```php
use Skpassegna\JsonParser\Exceptions\HumanReadableJsonException;

try {
    $data = $jsonParser->parse('invalid json string');
} catch (HumanReadableJsonException $e) {
    echo $e->getMessage(); // Output: "Invalid or malformed JSON data. The syntax is incorrect."
}
```

## Conclusion

The `skpassegna/json-parser` library provides a powerful and user-friendly way to work with JSON data in your PHP applications. With its intuitive API and advanced features like filtering, mapping, sorting, and nested data handling, this library can simplify your JSON-related tasks and help you write cleaner, more maintainable code.

Whether you're a beginner or an experienced developer, give `skpassegna/json-parser` a try in your next PHP project, and experience the power of easy JSON parsing!
