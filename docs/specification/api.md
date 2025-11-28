# JSON Parser Library - API Contracts Specification

**Version**: 1.0  
**Status**: Draft  
**Last Updated**: 2024  
**Purpose**: Comprehensive API reference with method signatures, parameters, return types, exceptions, lifecycle notes, and future refactor deltas

---

## Table of Contents

1. [Introduction](#introduction)
2. [Facade Layer](#facade-layer)
3. [Trait Contracts](#trait-contracts)
4. [Interface Contracts](#interface-contracts)
5. [Operation Classes](#operation-classes)
6. [Utility Classes](#utility-classes)
7. [Iterator & ArrayAccess Contracts](#iterator--arrayaccess-contracts)
8. [Streaming Interfaces](#streaming-interfaces)
9. [Error Conditions & Exception Mapping](#error-conditions--exception-mapping)
10. [Future Refactor Deltas](#future-refactor-deltas)

---

## Introduction

This document expands on Sections D & E of the Architecture Specification by providing detailed method contracts for every class and trait in the JSON Parser library. Each contract includes:

- **Method Signature**: PHP 8 function/method declaration with types
- **Parameters**: Complete parameter list with types, defaults, and descriptions
- **Return Type**: Explicit return type declaration
- **Throws**: All exceptions that can be thrown
- **Lifecycle Notes**: Mutability semantics, state effects, chaining capability
- **Future Delta**: Notes on planned enhancements or refactor requirements

---

## Facade Layer

### Class: `Json` (Implements `JsonInterface`)

**Namespace**: `Skpassegna\Json\Json`  
**Access**: `public`  
**Final**: No  
**Use of Traits**: `DataAccessTrait`, `TransformationTrait`  

**Properties**:
- `protected array|object $data = []` - Internal storage for JSON data

#### Static Methods

##### `parse()`

```php
public static function parse(
    string|array|object $input,
    array $options = []
): static
```

**Parameters**:
- `$input` (`string|array|object`): JSON string, associative array, or object to parse
- `$options` (`array<string, mixed>`): Parsing options (reserved for future use)

**Returns**: New instance with parsed data

**Throws**:
- `ParseException` - If JSON string is malformed
- `JsonException` (native) - From underlying json_decode failures

**Lifecycle**: Creates new instance. No mutability concerns. Chainable to instance methods.

**Future Delta**: Options array may support custom deserializers, strict mode parsing, or streaming large JSON files.

##### `create()`

```php
public static function create(): static
```

**Parameters**: None

**Returns**: New empty instance with `[]` as initial data

**Throws**: None

**Lifecycle**: Creates new instance. Safe entry point for fluent chains.

**Future Delta**: May accept optional initial data parameter.

#### Instance Constructor

##### `__construct()`

```php
public function __construct(array|object $data = []): void
```

**Parameters**:
- `$data` (`array|object`): Initial data to store (default: `[]`)

**Returns**: Void

**Throws**: None

**Lifecycle**: Initializes instance with provided data. Data is stored by reference internally.

**Future Delta**: May validate data structure or normalize input types.

#### Core Access Methods

##### `getData()`

```php
public function getData(): mixed
```

**Parameters**: None

**Returns**: Current `$data` (array or object)

**Throws**: None

**Lifecycle**: Returns reference to internal state. Modifications affect instance.

**Future Delta**: May return immutable wrapper or snapshot to prevent external mutations.

##### `get()`

```php
public function get(
    string $path,
    mixed $default = null
): mixed
```

**Parameters**:
- `$path` (`string`): Dot-notation path (e.g., `"user.profile.name"`) or JSONPath (e.g., `"$.users[0].id"`)
- `$default` (`mixed`): Value returned if path not found (default: `null`)

**Returns**: Value at path, or `$default` if not found

**Throws**: None (returns default on missing paths)

**Lifecycle**: Read-only. Traverses nested structures safely.

**Interaction Notes**: Detects JSONPath via `$` prefix and delegates to `JsonPath` utility; otherwise uses dot notation.

**Future Delta**: May support filter expressions or recursive descent notation.

##### `set()`

```php
public function set(
    string $path,
    mixed $value
): static
```

**Parameters**:
- `$path` (`string`): Dot-notation path for where to set value
- `$value` (`mixed`): Value to assign

**Returns**: `$this` for method chaining

**Throws**: None (creates intermediate structures as needed)

**Lifecycle**: **Mutates** internal state. Returns `$this` for fluent chaining. Creates missing intermediate keys as arrays.

**Future Delta**: May support JSONPath syntax. May require explicit intermediate type hints (array vs object).

##### `remove()`

```php
public function remove(string $path): static
```

**Parameters**:
- `$path` (`string`): Dot-notation path to remove

**Returns**: `$this` for method chaining

**Throws**: None (silently returns `$this` if path doesn't exist)

**Lifecycle**: **Mutates** internal state by unsetting the key at path. Returns `$this` for chaining.

**Future Delta**: May raise exception on non-existent paths (configurable strict mode).

##### `has()`

```php
public function has(string $path): bool
```

**Parameters**:
- `$path` (`string`): Dot-notation path to check

**Returns**: `true` if path exists and is accessible, `false` otherwise

**Throws**: None

**Lifecycle**: Read-only. Traverses nested structures safely.

#### Validation Methods

##### `validateSchema()`

```php
public function validateSchema(
    array|object $schema
): bool
```

**Parameters**:
- `$schema` (`array|object`): JSON Schema (array or object) to validate against

**Returns**: `true` if valid, `false` otherwise

**Throws**:
- `ValidationException` - If schema is invalid or validation setup fails

**Lifecycle**: Read-only. Delegates to `Schema\Validator` internally.

**Future Delta**: May support caching validated schemas. May return detailed error objects instead of boolean.

#### Serialization Methods

##### `toString()`

```php
public function toString(
    int $options = 0,
    int $depth = 512
): string
```

**Parameters**:
- `$options` (`int`): JSON encoding flags (e.g., `JSON_PRETTY_PRINT`)
- `$depth` (`int`): Maximum nesting depth for encoding (default: 512)

**Returns**: JSON-encoded string

**Throws**:
- `JsonException` (native) - If encoding fails (e.g., unsupported types, max depth exceeded)

**Lifecycle**: Read-only. Produces output string without modifying state.

**Future Delta**: May wrap `JsonException` in custom exception type.

#### Query Methods

##### `query()`

```php
public function query(string $path): array
```

**Parameters**:
- `$path` (`string`): JSONPath expression (e.g., `"$.users[0].name"`)

**Returns**: Array of matching values (empty array if no matches)

**Throws**: None (returns empty array on invalid paths or no matches)

**Lifecycle**: Read-only. Delegates to `JsonPath` utility.

**Future Delta**: May support additional JSONPath features (recursive descent, filters).

#### Merge Methods

##### `merge()`

```php
public function merge(
    JsonInterface|array $source,
    bool $recursive = true
): static
```

**Parameters**:
- `$source` (`JsonInterface|array`): Data to merge into current instance
- `$recursive` (`bool`): Recursively merge nested arrays (default: `true`)

**Returns**: `$this` for method chaining

**Throws**: None

**Lifecycle**: **Mutates** internal state. Merges `$source` into current data.

**Interaction Notes**: If `$source` is `JsonInterface`, extracts underlying data via `getData()`.

**Future Delta**: May support merge strategies (deep, shallow, union, intersection).

##### `mergeJson()`

```php
public function mergeJson(
    mixed $source,
    string $strategy = JsonMerge::MERGE_RECURSIVE
): static
```

**Parameters**:
- `$source` (`mixed`): Data to merge from
- `$strategy` (`string`): Merge strategy constant (default: `JsonMerge::MERGE_RECURSIVE`)

**Returns**: `$this` for method chaining

**Throws**: None

**Lifecycle**: **Mutates** internal state via `JsonMerge` utility.

#### Pointer Operations (delegated)

##### `getPointer()`

```php
public function getPointer(string $pointer): mixed
```

**Parameters**:
- `$pointer` (`string`): JSON Pointer (RFC 6901) expression (e.g., `"/foo/0/bar"`)

**Returns**: Value at pointer location

**Throws**:
- `PathException` - If pointer is invalid or path not found

**Lifecycle**: Read-only. Delegates to `JsonPointer` utility.

**Future Delta**: May support relative pointers.

##### `setPointer()`

```php
public function setPointer(
    string $pointer,
    mixed $value
): static
```

**Parameters**:
- `$pointer` (`string`): JSON Pointer expression
- `$value` (`mixed`): Value to set

**Returns**: `$this` for method chaining

**Throws**:
- `PathException` - If pointer is invalid or cannot be set

**Lifecycle**: **Mutates** internal state. Delegates to `JsonPointer` utility.

---

## Trait Contracts

### Trait: `DataAccessTrait`

**Namespace**: `Skpassegna\Json\Traits\DataAccessTrait`  
**Requires**: Host class must have `protected $data` property  
**Use Pattern**: Horizontal composition providing array-like data access operations

#### Methods

##### `keys()`

```php
public function keys(): array<string|int>
```

**Returns**: Array of keys from `$this->data` (empty array if not array)

**Throws**: None

**Lifecycle**: Read-only. Returns copy of keys, not references.

##### `values()`

```php
public function values(): array
```

**Returns**: Array of values from `$this->data` (empty array if not array)

**Throws**: None

**Lifecycle**: Read-only. Returns copy of values.

##### `count()`

```php
public function count(): int
```

**Returns**: Number of elements in `$this->data` (0 if not array)

**Throws**: None

**Lifecycle**: Read-only. Non-negative integer.

**Future Delta**: May implement `Countable` interface.

##### `isEmpty()`

```php
public function isEmpty(): bool
```

**Returns**: `true` if `$this->data` is empty, `false` otherwise

**Throws**: None

**Lifecycle**: Read-only.

##### `toArray()`

```php
public function toArray(): array
```

**Returns**: `$this->data` cast to array

**Throws**: None

**Lifecycle**: Read-only. Returns copy of data as array.

**Future Delta**: May accept depth parameter for recursive conversion.

##### `toObject()`

```php
public function toObject(): object
```

**Returns**: `$this->data` cast to object (stdClass or object type)

**Throws**: None

**Lifecycle**: Read-only. Returns copy of data as object.

##### `filter()`

```php
public function filter(callable $callback): static
```

**Parameters**:
- `$callback` (`callable`): Function `fn(mixed $value, string|int $key): bool` returning true to keep element

**Returns**: New instance with filtered data

**Throws**: None

**Lifecycle**: **Immutable**. Creates clone with filtered data. Original unchanged.

**Interaction Notes**: Uses `ARRAY_FILTER_USE_BOTH` to pass both value and key to callback.

##### `map()`

```php
public function map(callable $callback): static
```

**Parameters**:
- `$callback` (`callable`): Function `fn(mixed $value): mixed` transforming each element

**Returns**: New instance with mapped data

**Throws**: None

**Lifecycle**: **Immutable**. Creates clone with transformed data.

##### `reduce()`

```php
public function reduce(
    callable $callback,
    mixed $initial = null
): mixed
```

**Parameters**:
- `$callback` (`callable`): Function `fn(mixed $carry, mixed $item): mixed`
- `$initial` (`mixed`): Initial accumulator value (default: `null`)

**Returns**: Final accumulated value

**Throws**: None

**Lifecycle**: Read-only. Reduces to scalar/single value.

##### `sort()`

```php
public function sort(?callable $callback = null): static
```

**Parameters**:
- `$callback` (`callable|null`): Optional comparator function `fn(mixed $a, mixed $b): int`

**Returns**: New instance with sorted data

**Throws**: None

**Lifecycle**: **Immutable**. Creates clone with sorted data. Maintains key-value associations.

**Implementation Detail**: Uses `uasort()` to preserve keys.

##### `slice()`

```php
public function slice(
    int $offset,
    ?int $length = null
): static
```

**Parameters**:
- `$offset` (`int`): Starting position (can be negative)
- `$length` (`int|null`): Number of elements to extract (null = to end)

**Returns**: New instance with sliced data

**Throws**: None

**Lifecycle**: **Immutable**. Creates clone with subset of data.

##### `find()`

```php
public function find(callable $callback): array
```

**Parameters**:
- `$callback` (`callable`): Function `fn(mixed $value, string|int $key): bool`

**Returns**: Array of matching values (empty array if none match)

**Throws**: None

**Lifecycle**: Read-only. Recursively walks data structure.

##### `first()`

```php
public function first(?callable $callback = null): mixed
```

**Parameters**:
- `$callback` (`callable|null`): Optional filter function. If null, returns first element.

**Returns**: First element (or first matching if callback provided), or `null` if empty/no match

**Throws**: None

**Lifecycle**: Read-only.

##### `last()`

```php
public function last(?callable $callback = null): mixed
```

**Parameters**:
- `$callback` (`callable|null`): Optional filter function. If null, returns last element.

**Returns**: Last element (or last matching if callback provided), or `null` if empty/no match

**Throws**: None

**Lifecycle**: Read-only.

##### `get()`

```php
public function get(
    string $path,
    mixed $default = null
): mixed
```

**Parameters**:
- `$path` (`string`): Dot-notation path
- `$default` (`mixed`): Default value if path not found

**Returns**: Value at path or default

**Throws**: None

**Lifecycle**: Read-only. Handles both array and object traversal.

**Note**: Duplicated in `Json` class; trait version is foundational.

##### `set()`

```php
public function set(
    string $path,
    mixed $value
): static
```

**Parameters**:
- `$path` (`string`): Dot-notation path
- `$value` (`mixed`): Value to set

**Returns**: `$this`

**Throws**: None

**Lifecycle**: **Mutates** internal state. Creates intermediate structures as needed (arrays or stdClass objects).

---

### Trait: `TransformationTrait`

**Namespace**: `Skpassegna\Json\Traits\TransformationTrait`  
**Requires**: Host class must have `protected $data` property and `toString(int, int): string` method  
**Use Pattern**: Horizontal composition providing format transformation operations

#### Methods

##### `toXml()`

```php
public function toXml(string $rootElement = 'root'): string
```

**Parameters**:
- `$rootElement` (`string`): Root XML element name (default: `'root'`)

**Returns**: Formatted XML string

**Throws**:
- `TransformException` - If XML creation or formatting fails

**Lifecycle**: Read-only. Produces formatted XML output via DOMDocument.

**Future Delta**: May support namespace handling or attribute mapping options.

##### `toYaml()`

```php
public function toYaml(
    int $inline = 2,
    int $indent = 4
): string
```

**Parameters**:
- `$inline` (`int`): Nesting level where YAML switches to inline style (default: 2)
- `$indent` (`int`): Spaces per indentation level (default: 4)

**Returns**: YAML-formatted string

**Throws**:
- `TransformException` - If YAML conversion fails

**Lifecycle**: Read-only. Delegates to Symfony Yaml component.

**Dependencies**: `symfony/yaml` package required.

##### `fromHtml()`

```php
public function fromHtml(
    string $html,
    array $options = []
): static
```

**Parameters**:
- `$html` (`string`): HTML string to parse
- `$options` (`array`): Options including `'excludeTags' => []` for tags to skip

**Returns**: New instance with parsed HTML structure as JSON

**Throws**:
- `TransformException` - If HTML parsing fails

**Lifecycle**: Creates new instance. Does not mutate current state.

**Dependencies**: `symfony/dom-crawler` package required.

**Note**: Returns new instance rather than `$this`.

##### `fromJson5()`

```php
public function fromJson5(string $json5): static
```

**Parameters**:
- `$json5` (`string`): JSON5 string (supports comments, trailing commas, etc.)

**Returns**: New instance with parsed JSON5 data

**Throws**:
- `TransformException` - If JSON5 parsing fails

**Lifecycle**: Creates new instance.

**Dependencies**: `colinodell/json5` package required.

##### `toCsv()`

```php
public function toCsv(
    array $headers = [],
    string $delimiter = ',',
    string $enclosure = '"',
    string $escape = '\\'
): string
```

**Parameters**:
- `$headers` (`array`): Column headers (auto-detected from first row if empty)
- `$delimiter` (`string`): Field separator (default: `,`)
- `$enclosure` (`string`): Quote character (default: `"`)
- `$escape` (`string`): Escape character (default: `\`)

**Returns**: CSV-formatted string

**Throws**:
- `TransformException` - If data is not array or CSV conversion fails

**Lifecycle**: Read-only. Uses internal temp stream for generation.

**Requirement**: `$this->data` must be array of rows (each row must be array).

##### `prettyPrint()`

```php
public function prettyPrint(
    int $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
): string
```

**Parameters**:
- `$options` (`int`): JSON encoding flags (default: pretty, unescaped)

**Returns**: Formatted JSON string

**Throws**:
- `JsonException` (native) - If encoding fails

**Lifecycle**: Read-only. Delegates to `toString()` method.

##### `minify()`

```php
public function minify(): string
```

**Parameters**: None

**Returns**: Minified JSON string (no whitespace)

**Throws**:
- `JsonException` (native) - If encoding fails

**Lifecycle**: Read-only. Produces compact output.

##### `transform()`

```php
public function transform(callable $callback): static
```

**Parameters**:
- `$callback` (`callable`): Function `fn(mixed $data): mixed` transforming the entire data structure

**Returns**: New instance with transformed data

**Throws**: Any exceptions raised by `$callback`

**Lifecycle**: **Immutable**. Creates clone with callback result as new data.

##### `flatten()`

```php
public function flatten(): static
```

**Parameters**: None

**Returns**: New instance with flattened data structure

**Throws**: None

**Lifecycle**: **Immutable**. Converts nested structure to single level using dot notation keys.

**Example**: `['user' => ['name' => 'John']]` becomes `['user.name' => 'John']`

##### `unflatten()`

```php
public function unflatten(string $delimiter = '.'): static
```

**Parameters**:
- `$delimiter` (`string`): Delimiter used in flat keys (default: `.`)

**Returns**: New instance with unflattened nested structure

**Throws**: None

**Lifecycle**: **Immutable**. Reconstructs nested structure from flat keys.

**Example**: `['user.name' => 'John']` becomes `['user' => ['name' => 'John']]`

---

## Interface Contracts

### Interface: `JsonInterface`

**Namespace**: `Skpassegna\Json\Contracts\JsonInterface`  
**Implemented By**: `Json` class  
**Purpose**: Contract for core JSON operations

```php
interface JsonInterface
{
    public static function parse(string|array|object $input, array $options = []): static;
    public static function create(): static;
    public function getData(): mixed;
    public function get(string $path, mixed $default = null): mixed;
    public function set(string $path, mixed $value): static;
    public function remove(string $path): static;
    public function has(string $path): bool;
    public function validateSchema(array|object $schema): bool;
    public function toString(int $options = 0, int $depth = 512): string;
    public function query(string $path): array;
    public function merge(self|array $source, bool $recursive = true): static;
}
```

**Contract Notes**:
- Static methods must return `static` for fluent subclass instantiation
- Instance methods support method chaining via `static` return
- `validateSchema()` requires external JSON Schema validator dependency
- No implicit serialization (no magic `__toString()`)

---

### Interface: `PointerInterface`

**Namespace**: `Skpassegna\Json\Contracts\PointerInterface`  
**Implemented By**: `Pointer` class  
**Purpose**: RFC 6901 JSON Pointer operations

```php
interface PointerInterface
{
    public function get(string|array $document, string $pointer): mixed;
    public function set(string|array $document, string $pointer, mixed $value, bool $mutate = false): string|array;
    public function remove(string|array $document, string $pointer, bool $mutate = false): string|array;
    public function has(string|array $document, string $pointer): bool;
    public function create(array $segments): string;
}
```

**Contract Notes**:
- Operates on documents (string JSON or array/object)
- `$mutate = true` modifies original; `false` returns deep copy
- Return type matches input type (string input → string output)
- `create()` generates pointer string from path segments

---

### Interface: `PatchInterface`

**Namespace**: `Skpassegna\Json\Contracts\PatchInterface`  
**Implemented By**: `Patch` class  
**Purpose**: RFC 6902 JSON Patch operations

```php
interface PatchInterface
{
    public function apply(string|array $document, string|array $patch, bool $mutate = false): string|array;
    public function diff(string|array $source, string|array $target): string;
    public function test(string|array $document, string|array $patch): bool;
}
```

**Contract Notes**:
- `apply()` executes patch operations (add, remove, replace, move, copy, test)
- `diff()` generates patch operations transforming source to target
- `test()` validates patch applicability without modifying
- Operations follow RFC 6902 semantics

---

### Interface: `SerializerInterface`

**Namespace**: `Skpassegna\Json\Contracts\SerializerInterface`  
**Implemented By**: `Serializer` class  
**Purpose**: JSON encoding/decoding operations

```php
interface SerializerInterface
{
    public function serialize(mixed $data, int $flags = 0, int $depth = 512): string;
    public function deserialize(string $json, bool $associative = false, int $depth = 512, int $flags = 0): mixed;
}
```

**Contract Notes**:
- `serialize()` wraps `json_encode()` with error handling
- `deserialize()` wraps `json_decode()` with error handling
- Flags are passed directly to native functions
- Both may throw `RuntimeException` on encoding/decoding errors

---

### Interface: `TransformerInterface`

**Namespace**: `Skpassegna\Json\Contracts\TransformerInterface`  
**Implemented By**: `Transformer` class  
**Purpose**: Format transformation operations

```php
interface TransformerInterface
{
    public function toXml(string $json): string;
    public function toYaml(string $json): string;
    public function toCsv(string $json, string $delimiter = ',', string $enclosure = '"', string $escape = '\\'): string;
}
```

**Contract Notes**:
- Input is always JSON string (not generic data)
- Output is transformed format string
- May require external dependencies (YAML extension, etc.)

---

### Interface: `ValidatorInterface`

**Namespace**: `Skpassegna\Json\Contracts\ValidatorInterface`  
**Implemented By**: `Validator` class  
**Purpose**: JSON validation operations

```php
interface ValidatorInterface
{
    public function isValid(string $json): bool;
    public function validateSchema(mixed $data, string|object $schema): bool;
    public function getErrors(): array;
}
```

**Contract Notes**:
- `isValid()` checks JSON format only
- `validateSchema()` validates against JSON Schema
- `getErrors()` returns error messages from last validation

---

### Interface: `HtmlConverterInterface`

**Namespace**: `Skpassegna\Json\Contracts\HtmlConverterInterface`  
**Implemented By**: `HtmlConverter` class  
**Purpose**: HTML to JSON conversion

```php
interface HtmlConverterInterface
{
    public function convert(string $html, array $options = []): string;
}
```

**Contract Notes**:
- Input is HTML markup string
- Output is JSON string representation of structure
- Options may control tag filtering, attribute inclusion, etc.

---

## Operation Classes

### Class: `Pointer`

**Namespace**: `Skpassegna\Json\Json\Pointer`  
**Implements**: `PointerInterface`  
**Access**: `public`  
**Properties**:
- `private Serializer $serializer`

#### Methods

##### `__construct()`

```php
public function __construct(): void
```

**Initializes** internal `Serializer` instance.

##### `get()`

```php
public function get(string|array $document, string $pointer): mixed
```

**Returns**: Value at pointer location

**Throws**:
- `RuntimeException` - If path not found or pointer format invalid

**Note**: See `PointerInterface` for full contract.

##### `set()`

```php
public function set(
    string|array $document,
    string $pointer,
    mixed $value,
    bool $mutate = false
): string|array
```

**Returns**: Modified document (type matches input)

**Throws**:
- `RuntimeException` - If pointer format invalid

**Note**: Handles special `-` segment for array append.

##### `remove()`

```php
public function remove(
    string|array $document,
    string $pointer,
    bool $mutate = false
): string|array
```

**Returns**: Modified document

**Throws**:
- `RuntimeException` - If pointer is empty string (cannot remove root) or path not found

**Note**: Re-indexes numeric arrays after removal.

##### `has()`

```php
public function has(string|array $document, string $pointer): bool
```

**Returns**: `true` if pointer exists, `false` otherwise

**Throws**: None (catches all exceptions, returns false)

##### `create()`

```php
public function create(array $segments): string
```

**Returns**: JSON Pointer string

**Implementation**: Encodes segments (escapes `~` as `~0`, `/` as `~1`), joins with `/` prefix.

---

### Class: `Patch`

**Namespace**: `Skpassegna\Json\Json\Patch`  
**Implements**: `PatchInterface`  
**Access**: `public`  
**Properties**:
- `private Serializer $serializer`
- `private Pointer $pointer`

#### Methods

##### `__construct()`

```php
public function __construct(): void
```

**Initializes** `Serializer` and `Pointer` instances.

##### `apply()`

```php
public function apply(
    string|array $document,
    string|array $patch,
    bool $mutate = false
): string|array
```

**Returns**: Patched document

**Throws**:
- `RuntimeException` - If patch format invalid, operations fail, or test conditions fail

**Operations Supported**:
- `add` - Insert/set value
- `remove` - Delete value
- `replace` - Update existing value (fails if not exists)
- `move` - Move value from one path to another
- `copy` - Copy value from one path to another
- `test` - Assert value at path matches expected (fails if mismatch)

**Each Operation Structure**:
```php
[
    'op' => string,      // 'add', 'remove', 'replace', 'move', 'copy', 'test'
    'path' => string,    // JSON Pointer path
    'value' => mixed,    // For 'add', 'replace', 'test'
    'from' => string,    // For 'move', 'copy'
]
```

##### `diff()`

```php
public function diff(
    string|array $source,
    string|array $target
): string
```

**Returns**: JSON Patch operations string

**Throws**:
- `RuntimeException` - If diff generation fails

**Algorithm**: Recursive comparison generating replace/add/remove operations.

##### `test()`

```php
public function test(
    string|array $document,
    string|array $patch
): bool
```

**Returns**: `true` if patch can be applied, `false` if application would fail

**Throws**: None

---

### Class: `Serializer`

**Namespace**: `Skpassegna\Json\Json\Serializer`  
**Implements**: `SerializerInterface`  
**Access**: `public`  

#### Methods

##### `serialize()`

```php
public function serialize(
    mixed $data,
    int $flags = 0,
    int $depth = 512
): string
```

**Returns**: JSON-encoded string

**Throws**:
- `RuntimeException` - On encoding failure with previous `JsonException` attached

**Flags Default**: `JSON_THROW_ON_ERROR` always added to ensure exceptions.

##### `deserialize()`

```php
public function deserialize(
    string $json,
    bool $associative = false,
    int $depth = 512,
    int $flags = 0
): mixed
```

**Returns**: Decoded PHP value

**Throws**:
- `RuntimeException` - On decoding failure with previous `JsonException` attached

---

### Class: `Validator`

**Namespace**: `Skpassegna\Json\Json\Validator`  
**Implements**: `ValidatorInterface`  
**Access**: `public`  
**Properties**:
- `private array $errors = []`

#### Methods

##### `isValid()`

```php
public function isValid(string $json): bool
```

**Returns**: `true` if JSON is well-formed, `false` otherwise

**Throws**: None

**Lifecycle**: Clears `$errors` on success, populates on failure.

##### `validateSchema()`

```php
public function validateSchema(
    mixed $data,
    string|object $schema
): bool
```

**Returns**: `true` if data validates against schema, `false` otherwise

**Throws**:
- `RuntimeException` - If `justinrainbow/json-schema` not installed
- `InvalidArgumentException` - If schema is invalid

**Lifecycle**: Populates `$errors` with validation failures.

**Dependencies**: `justinrainbow/json-schema` package required.

##### `getErrors()`

```php
public function getErrors(): array
```

**Returns**: Array of error message strings from last validation

**Format**: For schema validation, errors formatted as `"[path] message"`.

---

### Class: `Transformer`

**Namespace**: `Skpassegna\Json\Json\Transformer`  
**Implements**: `TransformerInterface`  
**Access**: `public`  
**Properties**:
- `private Serializer $serializer`

#### Methods

##### `__construct()`

```php
public function __construct(): void
```

**Initializes** `Serializer` instance.

##### `toXml()`

```php
public function toXml(string $json): string
```

**Returns**: XML string with root element

**Throws**:
- `RuntimeException` - If transformation fails

**Implementation**: Deserialize JSON, traverse array recursively, build SimpleXMLElement, format with DOMDocument.

##### `toYaml()`

```php
public function toYaml(string $json): string
```

**Returns**: YAML-formatted string

**Throws**:
- `RuntimeException` - If YAML extension not available or transformation fails

**Dependencies**: PHP `yaml` extension required.

##### `toCsv()`

```php
public function toCsv(
    string $json,
    string $delimiter = ',',
    string $enclosure = '"',
    string $escape = '\\'
): string
```

**Returns**: CSV-formatted string

**Throws**:
- `RuntimeException` - If JSON is not array of rows or conversion fails

**Requirement**: JSON must deserialize to array of associative arrays with consistent keys.

---

### Class: `MergePatch`

**Namespace**: `Skpassegna\Json\Json\MergePatch`  
**Implements**: `MergePatchInterface`  
**Access**: `public`  
**Properties**:
- `private Serializer $serializer`

#### Methods

##### `__construct()`

```php
public function __construct(): void
```

**Initializes** `Serializer` instance.

##### `apply()`

```php
public function apply(
    string|array $target,
    string|array $patch,
    bool $mutate = false
): string|array
```

**Returns**: Patched document (type matches input)

**Throws**:
- `RuntimeException` - If merge patch fails

**RFC 7396 Semantics**:
- If patch value is `null`, remove key from target
- If patch is object/array, recursively merge into target
- Non-object/array patch values replace target

##### `diff()`

```php
public function diff(
    string|array $source,
    string|array $target
): string
```

**Returns**: JSON Merge Patch string

**Throws**:
- `RuntimeException` - If diff generation fails

---

### Class: `Path`

**Namespace**: `Skpassegna\Json\Json\Path`  
**Implements**: `PathInterface`  
**Access**: `public`  
**Properties**:
- `private Serializer $serializer`
- `private array $cache = []`

#### Methods

##### `__construct()`

```php
public function __construct(): void
```

**Initializes** `Serializer` and path cache.

##### `query()`

```php
public function query(
    string|array $document,
    string $path
): array
```

**Returns**: Array of matching values

**Throws**:
- `RuntimeException` - If query evaluation fails

**JSONPath Support**:
- `$` - Root
- `.` - Child operator
- `..` - Recursive descent
- `[]` - Subscript
- `*` - Wildcard
- `[start:end:step]` - Slice notation
- `[?(@.property op value)]` - Filter expressions

**Caching**: Parsed paths are cached to avoid re-parsing.

---

## Utility Classes

### Class: `JsonPointer` (Static Utility)

**Namespace**: `Skpassegna\Json\Utils\JsonPointer`  
**Access**: `public`  
**All Methods**: `public static`

#### Static Methods

##### `get()`

```php
public static function get(mixed $data, string $pointer): mixed
```

**Returns**: Value at pointer location

**Throws**:
- `PathException` - If pointer invalid, path not found, or traversal fails

**Requirement**: `$pointer` must start with `/`.

##### `set()`

```php
public static function set(
    mixed &$data,
    string $pointer,
    mixed $value
): mixed
```

**Parameters**: `$data` passed by reference

**Returns**: The modified `$data`

**Throws**:
- `PathException` - If pointer invalid or value cannot be set

**Side Effect**: Modifies `$data` by reference.

##### `parsePointer()`

```php
private static function parsePointer(string $pointer): array
```

**Returns**: Array of decoded path segments

**Decoding**: Replaces `~1` with `/`, `~0` with `~`.

---

### Class: `JsonPath` (Query Utility)

**Namespace**: `Skpassegna\Json\Utils\JsonPath`  
**Access**: `public`  
**Properties**:
- `private mixed $data`

#### Methods

##### `__construct()`

```php
public function __construct(mixed $data): void
```

**Stores** data for querying.

##### `query()`

```php
public function query(string $path): array
```

**Returns**: Array of values matching JSONPath expression

**Throws**: None (returns empty array on invalid paths)

**Path Syntax**:
- `$` - Root implicit
- `.property` - Object property access
- `[index]` - Array index or object key
- `[*]` - All elements
- `[start:end:step]` - Array slice

---

### Class: `JsonMerge` (Static Utility)

**Namespace**: `Skpassegna\Json\Utils\JsonMerge`  
**Access**: `public`  
**All Methods**: `public static`

#### Constants

```php
const MERGE_RECURSIVE = 'recursive';
const MERGE_SIMPLE = 'simple';
```

#### Static Methods

##### `merge()`

```php
public static function merge(
    mixed $source,
    mixed $target,
    string $strategy = self::MERGE_RECURSIVE
): mixed
```

**Parameters**:
- `$source` - Source data structure
- `$target` - Target data structure
- `$strategy` - `'recursive'` or `'simple'`

**Returns**: Merged result

**Throws**: None

**Behavior**:
- `MERGE_RECURSIVE` - Deep merge with target values overriding
- `MERGE_SIMPLE` - Shallow merge

---

### Class: `HtmlConverter` (Static Utility)

**Namespace**: `Skpassegna\Json\Utils\HtmlConverter`  
**Access**: `public`  
**All Methods**: `public static`

#### Static Methods

##### `toJson()`

```php
public static function toJson(string $html, array $options = []): string
```

**Parameters**:
- `$html` - HTML markup string
- `$options` - Configuration (e.g., `excludeTags`, `includeAttributes`)

**Returns**: JSON string representing HTML structure

**Throws**:
- `RuntimeException` - If HTML parsing fails

**Dependencies**: `symfony/dom-crawler` package required.

---

### Class: `Json5Handler` (Static Utility)

**Namespace**: `Skpassegna\Json\Utils\Json5Handler`  
**Access**: `public`  
**All Methods**: `public static`

#### Static Methods

##### `decode()`

```php
public static function decode(string $json5): mixed
```

**Returns**: Decoded JSON5 data

**Throws**:
- `RuntimeException` - If JSON5 parsing fails

**Dependencies**: `colinodell/json5` package required.

**Supports**:
- Trailing commas
- Single-line and multi-line comments
- Unquoted object keys
- Single-quoted strings

---

## Iterator & ArrayAccess Contracts

### Future Interface: `IteratorInterface` (Planned)

**Namespace**: `Skpassegna\Json\Contracts\IteratorInterface`  
**Planned Implementation**: `Json` class with `IteratorAggregate`

```php
interface IteratorInterface extends IteratorAggregate, Countable
{
    public function getIterator(): Traversable;
    public function count(): int;
}
```

**Lifecycle Notes**:
- Enables `foreach()` iteration over JSON data
- Works with `count()` function
- Supports array union operations (`+` operator)

**Future Delta**: May implement full `Iterator` interface with pointer control or `ArrayAccess` for offset notation.

---

### Future Interface: `ArrayAccessInterface` (Planned)

**Namespace**: `Skpassegna\Json\Contracts\ArrayAccessInterface`  
**Planned Implementation**: `Json` class with `ArrayAccess`

```php
interface ArrayAccessInterface extends ArrayAccess
{
    public function offsetExists(mixed $offset): bool;
    public function offsetGet(mixed $offset): mixed;
    public function offsetSet(mixed $offset, mixed $value): void;
    public function offsetUnset(mixed $offset): void;
}
```

**Lifecycle Notes**:
- Enables array-like access: `$json['key']`, `isset($json['key'])`
- Assignment via `$json['key'] = $value` mutates state
- Unset via `unset($json['key'])` removes key

**Future Delta**: May support dot-notation in bracket access or type hints for offset values.

---

## Streaming Interfaces

### Future Interface: `StreamingInterface` (Planned)

**Namespace**: `Skpassegna\Json\Contracts\StreamingInterface`  
**Planned Implementation**: New `StreamingParser` or `StreamingSerializer` classes

```php
interface StreamingInterface
{
    public function parseStream(resource $stream, int $chunkSize = 8192): Generator;
    public function serializeStream(mixed $data, resource $stream, int $chunkSize = 8192): int;
}
```

**Method Contract**:

#### `parseStream()`

```php
public function parseStream(
    resource $stream,
    int $chunkSize = 8192
): Generator
```

**Parameters**:
- `$stream` - Open readable stream resource
- `$chunkSize` - Bytes to read per iteration (default: 8192)

**Yields**: Parsed JSON fragments or complete values

**Throws**:
- `RuntimeException` - If stream reading or parsing fails

**Lifecycle**: Generator-based lazy parsing. No intermediate array buffering.

**Use Cases**: Processing large JSON files, streaming APIs, real-time data.

#### `serializeStream()`

```php
public function serializeStream(
    mixed $data,
    resource $stream,
    int $chunkSize = 8192
): int
```

**Parameters**:
- `$data` - Data to serialize
- `$stream` - Open writable stream resource
- `$chunkSize` - Bytes to write per flush (default: 8192)

**Returns**: Total bytes written

**Throws**:
- `RuntimeException` - If stream writing fails

**Lifecycle**: Streams JSON output without buffering entire result in memory.

### Future Interface: `TransformStreamInterface` (Planned)

**Namespace**: `Skpassegna\Json\Contracts\TransformStreamInterface`  

```php
interface TransformStreamInterface
{
    public function transformStream(
        resource $inputStream,
        resource $outputStream,
        string $fromFormat,
        string $toFormat,
        int $chunkSize = 8192
    ): int;
}
```

**Parameters**:
- `$inputStream` - Source stream (JSON format)
- `$outputStream` - Target stream
- `$fromFormat` - Source format ('json', 'xml', 'yaml', 'csv')
- `$toFormat` - Target format
- `$chunkSize` - Buffer size

**Returns**: Bytes written

**Throws**:
- `RuntimeException` - If transformation fails

**Lifecycle**: Streaming format conversion with buffering optimization.

---

## Error Conditions & Exception Mapping

### Exception Hierarchy

All exceptions inherit from `Skpassegna\Json\Exceptions\JsonException` (extends `\Exception`)

#### Exception Classes

##### `JsonException` (Base)

```php
class JsonException extends \Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null
    )
}
```

**Base class for all JSON library exceptions**. Always chain previous exceptions for debugging.

##### `ParseException`

**Thrown When**: JSON string cannot be parsed by `json_decode()`

**Example Context**:
```php
try {
    Json::parse('{ invalid json }');
} catch (ParseException $e) {
    // Handle parsing failure
}
```

##### `ValidationException`

**Thrown When**: JSON Schema validation fails or schema is invalid

**Example Context**:
```php
try {
    $json->validateSchema(['type' => 'object']);
} catch (ValidationException $e) {
    // Handle validation failure
}
```

##### `RuntimeException`

**Thrown When**: General operational failures (encoding, decoding, pointer operations, patching)

**Example Context**: JSON encoding fails due to circular references, max depth exceeded, unsupported type.

##### `PathException`

**Thrown When**: JSON Pointer path is invalid or inaccessible

**Example Context**: Malformed JSON Pointer, non-existent path traversal.

##### `TransformException`

**Thrown When**: Format transformation fails (JSON to XML, YAML, CSV, etc.)

**Example Context**: XML structure creation fails, YAML encoding fails, data unsuitable for CSV.

##### `InvalidArgumentException`

**Thrown When**: Invalid arguments passed to methods

**Example Context**: Invalid schema object, unsupported strategy parameter.

##### `IOException`

**Thrown When**: Stream or file operations fail

**Example Context**: Cannot read stream, stream closed unexpectedly. (Planned for streaming APIs)

---

### Exception Handling Patterns

#### Pattern 1: Graceful Degradation

```php
try {
    $json = Json::parse($input);
} catch (ParseException $e) {
    $json = Json::create(); // Fallback
}
```

#### Pattern 2: Error Details

```php
try {
    $json->validateSchema($schema);
} catch (ValidationException $e) {
    $errors = $e->getPrevious()?->getMessage();
}
```

#### Pattern 3: Chaining Previous

```php
catch (RuntimeException $e) {
    if ($e->getPrevious()) {
        // Original JsonException or other error
        log_error($e->getPrevious()->getMessage());
    }
}
```

---

## Future Refactor Deltas

### Delta 1: JsonDocument Class

**Current State**: Data stored directly in `Json` class.

**Future State**: Separate `JsonDocument` class encapsulating document metadata, version, schema.

**Planned Signature**:
```php
class JsonDocument
{
    public function __construct(
        mixed $data,
        ?string $schema = null,
        array $metadata = []
    ) {}
    
    public function getData(): mixed {}
    public function getSchema(): ?string {}
    public function getMetadata(string $key): mixed {}
    public function validate(): bool {}
}
```

**Migration Path**: `Json` class may wrap or inherit from `JsonDocument`.

---

### Delta 2: ValidatorTrait

**Current State**: Validation delegated via `ValidatorInterface`.

**Planned Trait**:
```php
trait ValidatorTrait
{
    private array $validationErrors = [];
    
    public function validate(array|object $schema): bool {}
    public function getValidationErrors(): array {}
    public function addValidator(ValidatorInterface $validator): static {}
}
```

**Integration**: Host class includes trait, maintains validator instances.

---

### Delta 3: SerializableTrait

**Current State**: Serialization via trait methods and delegated classes.

**Planned Trait**:
```php
trait SerializableTrait
{
    public function serialize(string $format = 'json'): string {}
    public function deserialize(string $input, string $format = 'json'): static {}
    public function supportsFormat(string $format): bool {}
}
```

**Formats**: `json`, `xml`, `yaml`, `csv`, `json5`.

---

### Delta 4: Enhanced Path Operations

**Current State**: Limited JSONPath support in utilities.

**Future Enhancement**:
```php
class EnhancedPath implements PathInterface
{
    public function query(string|array $document, string $path): array {}
    public function queryWithMetadata(string|array $document, string $path): array {} // Returns values + paths
    public function queryStream(resource $stream, string $path): Generator {}
    public function count(string|array $document, string $path): int {}
}
```

**New Features**:
- Recursive descent (`..`)
- Complex filter expressions
- Path metadata in results
- Stream-based querying

---

### Delta 5: Magic Methods

**Current State**: No magic method implementation.

**Planned Magic Methods**:
```php
class Json
{
    public function __get(string $name): mixed {} // Property access delegation
    public function __set(string $name, mixed $value): void {} // Property assignment
    public function __isset(string $name): bool {} // has() delegation
    public function __unset(string $name): void {} // remove() delegation
    public function __toString(): string {} // toString() delegation
    public function __invoke(string $path, mixed $value = null): mixed {} // get/set via callable
}
```

**Example Usage**:
```php
$json = Json::parse('{"name":"John"}');
echo $json->name;  // Calls __get(), returns "John"
$json->age = 30;   // Calls __set(), mutates data
isset($json->name); // Calls __isset()
```

---

### Delta 6: Lazy Loading & Caching

**Current State**: No internal caching mechanism.

**Planned Features**:
```php
interface CacheableInterface
{
    public function enableCache(CacheInterface $cache): static {}
    public function clearCache(): static {}
    public function isCached(string $key): bool {}
}

trait CacheableTrait
{
    private ?CacheInterface $cache = null;
    
    protected function cached(string $key, callable $compute): mixed {}
}
```

**Use Cases**: Cache frequently queried paths, parsed JSONPath expressions, schema validation results.

---

### Delta 7: Type Coercion & Normalization

**Current State**: Minimal type coercion.

**Future Enhancement**:
```php
class TypeCoercion
{
    public static function toJson(mixed $value): mixed {}
    public static function fromJson(mixed $value, string $targetType): mixed {}
    public static function normalize(mixed $value, array $rules = []): mixed {}
}
```

**Rules**: Type hints in schema could trigger automatic coercion (string to int, array to object, etc.).

---

### Delta 8: Diff/Merge Strategies

**Current State**: RFC 6902 and RFC 7396 only.

**Future Strategies**:
```php
enum MergeStrategy: string
{
    case RECURSIVE = 'recursive';
    case SIMPLE = 'simple';
    case DEEP_REPLACE = 'deep_replace';
    case UNION = 'union';
    case INTERSECTION = 'intersection';
    case CUSTOM = 'custom';
}

class StrategicMerge
{
    public function merge(
        mixed $source,
        mixed $target,
        MergeStrategy $strategy,
        ?callable $resolver = null
    ): mixed {}
}
```

**Use Cases**: Different merge behaviors for different domains (config files, API responses, etc.).

---

### Delta 9: Streaming Parser & Serializer

**Current State**: No streaming support.

**Planned Classes**:
```php
class StreamingJsonParser implements StreamingInterface
{
    public function parseStream(resource $stream, int $chunkSize = 8192): Generator {}
    public function parseJsonLines(resource $stream): Generator {}  // Newline-delimited JSON
}

class StreamingJsonSerializer implements StreamingInterface
{
    public function serializeStream(mixed $data, resource $stream, int $chunkSize = 8192): int {}
    public function serializeJsonLines(iterable $items, resource $stream): int {}
}
```

**Use Cases**: Large file processing, real-time data pipelines, memory-constrained environments.

---

### Delta 10: Listener/Event System

**Current State**: No event hooks.

**Future Interface**:
```php
interface EventListener
{
    public function onParse(ParseEvent $event): void;
    public function onValidate(ValidateEvent $event): void;
    public function onTransform(TransformEvent $event): void;
}

class Json implements JsonInterface
{
    public function addEventListener(EventListener $listener): static {}
    public function removeEventListener(EventListener $listener): static {}
}
```

**Events**: Before/after parse, validate, transform, merge operations for observability and debugging.

---

## Interaction Notes

### Dependency Injection Pattern

Classes accept dependencies via constructor:

```php
$serializer = new Serializer();
$pointer = new Pointer();
$patch = new Patch();
```

Each instance is independent; no singletons required.

### Immutability vs Mutability

**Immutable Methods** (return new instances):
- `filter()`, `map()`, `sort()`, `slice()` from `DataAccessTrait`
- `transform()`, `flatten()`, `unflatten()` from `TransformationTrait`
- Pointer operations with `$mutate = false`

**Mutable Methods** (modify in-place, return `$this`):
- `set()`, `remove()` from `DataAccessTrait` and `Json`
- `merge()`, `mergeJson()` from `Json`
- Pointer operations with `$mutate = true`

**Strategy**: Immutable methods clone data; mutable methods use reference semantics.

### Chaining & Fluency

All mutable methods return `$this` enabling fluent chains:

```php
$json->set('user.name', 'John')
     ->set('user.age', 30)
     ->set('user.email', 'john@example.com');
```

Immutable methods break chains (return new instance):

```php
$filtered = $json->filter(fn($v) => $v > 10);
// $json unchanged
// $filtered is new instance
```

---

## Acceptance Criteria Verification

✅ **Each class/trait from architecture spec has corresponding API block**
- Json facade: ✓
- DataAccessTrait: ✓
- TransformationTrait: ✓
- Pointer: ✓
- Patch: ✓
- Serializer: ✓
- Validator: ✓
- Transformer: ✓
- MergePatch: ✓
- Path: ✓
- Utilities (JsonPointer, JsonPath, JsonMerge, HtmlConverter, Json5Handler): ✓

✅ **PHP-friendly signatures**
- All methods use PHP 8 union types, named arguments, strict typing: ✓
- Return types explicitly declared: ✓
- Parameter types documented: ✓

✅ **Parameter/return typing complete**
- Every method includes parameter types: ✓
- Every method includes return type: ✓
- Nullable and union types used appropriately: ✓

✅ **Error conditions documented**
- All exceptions that can be thrown listed: ✓
- Exception hierarchy provided: ✓
- Exception mapping to conditions: ✓

✅ **Interaction notes covering Sections D, E**
- Dependency injection noted: ✓
- Mutability semantics documented: ✓
- Chaining capability explained: ✓
- Trait composition patterns shown: ✓

✅ **Iterator requirements covered**
- Future `IteratorInterface` contract provided: ✓
- Planned `ArrayAccess` implementation noted: ✓
- Generators for streaming documented: ✓

✅ **Streaming interfaces specified**
- Future `StreamingInterface` contract: ✓
- `StreamingParserInterface` planned: ✓
- `TransformStreamInterface` planned: ✓
- Use cases documented: ✓

✅ **Magic method behaviors specified**
- Planned `__get`, `__set`, `__isset`, `__unset`, `__toString`, `__invoke`: ✓
- Usage examples provided: ✓

✅ **Deltas for future refactor**
- 10 detailed delta sections covering planned enhancements: ✓
- Migration paths provided: ✓
- New classes/traits/interfaces outlined: ✓

---

**Document End**
