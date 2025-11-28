# JSON Parser Library - Architecture Specification

**Version**: 1.0  
**Status**: Draft  
**Last Updated**: 2024  
**Purpose**: Comprehensive blueprint for the advanced JSON management PHP 8+ library architecture

---

## Table of Contents

1. [Executive Summary](#executive-summary)
2. [Architectural Principles](#architectural-principles)
3. [Core Components Overview](#core-components-overview)
4. [PHP OOP Concepts Application](#php-oop-concepts-application)
5. [Layered Architecture](#layered-architecture)
6. [Component Inventory](#component-inventory)
7. [Inheritance & Type Hierarchies](#inheritance--type-hierarchies)
8. [Trait Usage & Composition](#trait-usage--composition)
9. [Interface Contracts](#interface-contracts)
10. [Exception Hierarchy](#exception-hierarchy)
11. [Design Patterns](#design-patterns)
12. [Directory Structure & Mapping](#directory-structure--mapping)

---

## Executive Summary

The JSON Parser Library is a sophisticated PHP 8+ Composer package built around a fluent **`Skpassegna\Json\Json`** facade that combines horizontal code reuse through traits with vertical specialization through interfaces and concrete implementations. The architecture exercises advanced PHP OOP concepts including encapsulation, abstraction, polymorphism, inheritance, traits, access modifiers, and type-system features. The library provides **40+ components** organized in modular layers: **Contracts** (interfaces), **Core Operations** (specialized JSON processors), **Utilities** (helpers), **Schema** (validation), **Exceptions** (error hierarchy), and **Traits** (reusable behaviors).

### Key Architectural Objectives

- **Modularity**: Each component has a single, well-defined responsibility
- **Extensibility**: Contracts enable alternative implementations
- **Type Safety**: Strict PHP 8 typing throughout
- **Fluency**: Chainable operations via trait-based composition
- **Standards Compliance**: RFC 6901 (JSON Pointer), RFC 6902 (JSON Patch), RFC 7396 (JSON Merge Patch)
- **Testability**: Dependency injection and clear separation of concerns

---

## Architectural Principles

### 1. **Facade Pattern**
The `Json` class serves as the primary entry point, abstracting complexity from consumers while delegating specialized operations to dedicated components.

### 2. **Composition Over Inheritance**
Traits (`DataAccessTrait`, `TransformationTrait`) provide horizontal composition for shared behaviors rather than deep inheritance chains.

### 3. **Interface Segregation**
Each contract (interface) is focused and minimal, allowing consumers to depend on specific behaviors without unnecessary baggage.

### 4. **Encapsulation**
Access modifiers (`private`, `protected`, `public`) enforce boundaries and hide implementation details.

### 5. **Immutability & Mutability**
Operations support both immutable (returning clones) and mutable (modifying in-place) variants where appropriate.

### 6. **Error Handling as Code**
Custom exceptions form a hierarchy enabling granular error handling and recovery strategies.

---

## Core Components Overview

### Facade Layer
- **`Json` class** - Main entry point combining traits and delegating to specialized operations

### Contract Layer (Interfaces)
- **`JsonInterface`** - Core JSON operations contract
- **`PointerInterface`** - JSON Pointer operations (RFC 6901)
- **`PatchInterface`** - JSON Patch operations (RFC 6902)
- **`ValidatorInterface`** - Schema validation contract
- **`SerializerInterface`** - Serialization/deserialization contract
- **`TransformerInterface`** - Format transformation contract
- **`HtmlConverterInterface`** - HTML ingestion contract

### Operation Layer (Implementations)
**Path Operations:**
- `Json/Path.php` - JSONPath query engine
- `Json/Pointer.php` - JSON Pointer (RFC 6901) implementation

**Patch Operations:**
- `Json/Patch.php` - JSON Patch (RFC 6902) implementation
- `Json/MergePatch.php` - JSON Merge Patch (RFC 7396) implementation

**Serialization & Transformation:**
- `Json/Serializer.php` - JSON encoding/decoding
- `Json/Transformer.php` - Format conversions (XML, YAML, CSV)
- `Json/HtmlConverter.php` - HTML to JSON conversion
- `Json/Minifier.php` - JSON minification utility

**Schema Operations:**
- `Json/Validator.php` - JSON validation layer
- `Json/SchemaGenerator.php` - JSON Schema generation

**Schema Validation:**
- `Schema/Validator.php` - Comprehensive schema validation with caching

### Utility Layer (Helpers)
- `Utils/JsonPath.php` - JSONPath helper with expression parsing
- `Utils/JsonPointer.php` - JSON Pointer static helpers
- `Utils/JsonMerge.php` - JSON merging strategies
- `Utils/HtmlConverter.php` - HTML conversion with configurable options
- `Utils/Json5Handler.php` - JSON5 parsing and comment extraction

### Trait Layer (Behaviors)
- `Traits/DataAccessTrait.php` - Data access operations (get, set, filter, map, reduce, sort, slice, find, first, last, keys, values, count, isEmpty, toArray, toObject)
- `Traits/TransformationTrait.php` - Data transformation operations (toXml, toYaml, toCsv, fromHtml, fromJson5, flatten, unflatten, prettyPrint, minify, transform)

### Exception Layer (Error Hierarchy)
- `Exceptions/JsonException.php` - Base exception
- `Exceptions/ParseException.php` - Parsing errors
- `Exceptions/ValidationException.php` - Validation errors with error tracking
- `Exceptions/PathException.php` - Path-related errors
- `Exceptions/TransformException.php` - Transformation errors
- `Exceptions/RuntimeException.php` - Generic runtime errors
- `Exceptions/IOException.php` - IO-related errors
- `Exceptions/InvalidArgumentException.php` - Invalid argument errors

---

## PHP OOP Concepts Application

### 1. **Encapsulation**
```
Access Modifiers Usage:
├─ Public: Facade methods, factory methods, primary APIs
├─ Protected: Helper methods available to subclasses
└─ Private: Internal implementation details (parsing, validation logic)

Examples:
- Json::parse()                   [public static factory]
- Json::get()                     [public instance]
- DataAccessTrait::flattenData()  [private helper]
- Validator::validateType()       [private validation logic]
```

### 2. **Abstraction**
```
Interface-based Contracts:
├─ JsonInterface              → Defines primary JSON operations
├─ PointerInterface           → Abstracts JSON Pointer operations
├─ PatchInterface             → Abstracts JSON Patch operations
├─ ValidatorInterface         → Abstracts validation strategy
├─ SerializerInterface        → Abstracts serialization strategy
├─ TransformerInterface       → Abstracts transformation strategy
└─ HtmlConverterInterface     → Abstracts HTML conversion strategy

Each interface provides explicit contract that implementations must satisfy.
```

### 3. **Inheritance**
```
Exception Hierarchy:
JsonException (Base)
├─ ParseException         (JSON parsing failures)
├─ ValidationException    (Schema validation failures)
├─ PathException          (Path-related errors)
├─ TransformException     (Transformation failures)
├─ RuntimeException       (Generic runtime errors)
├─ IOException            (File/stream errors)
└─ InvalidArgumentException (Invalid input errors)

All extend JsonException for unified error handling.
```

### 4. **Polymorphism**
```
Multiple Implementations of Common Contracts:

PointerInterface:
├─ Json/Pointer.php      (RFC 6901 compliant implementation)
└─ Utils/JsonPointer.php (Static helper variant)

SerializerInterface:
└─ Json/Serializer.php   (JSON encoding/decoding)

ValidatorInterface:
├─ Json/Validator.php      (Lightweight validation layer)
└─ Schema/Validator.php    (Comprehensive schema validation)

TransformerInterface:
└─ Json/Transformer.php  (Multiple format transformations)
```

### 5. **Traits (Horizontal Composition)**
```
DataAccessTrait:
├─ Data retrieval: get(), keys(), values(), first(), last()
├─ Collection ops: filter(), map(), reduce(), sort(), slice()
├─ Introspection: has(), count(), isEmpty()
├─ Conversion: toArray(), toObject()
└─ Search: find()

TransformationTrait:
├─ Format conversion: toXml(), toYaml(), toCsv()
├─ Ingestion: fromHtml(), fromJson5()
├─ Structure ops: flatten(), unflatten()
├─ Output formatting: prettyPrint(), minify()
└─ Custom transforms: transform()

Both composed into Json facade via use statements.
```

### 6. **Type System Features**

#### Union Types
```php
// Multiple type acceptance
public static function parse(string|array|object $input): static

// Union return types
public function getData(): mixed

// Flexible method signatures
public function set(string $pointer, mixed $value, bool $mutate = false): string|array
```

#### Named Arguments
```php
// Explicit parameter passing
json_decode(
    $input,
    associative: true,
    flags: JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING
);
```

#### Strict Typing
```php
declare(strict_types=1);  // Present in all files
```

#### Generic Documentation
```php
/**
 * @param array<string, mixed> $options  Configuration options
 * @return array<mixed>                  Query results
 */
```

### 7. **Constructors & Initialization**
```
Constructor Patterns:

Json::__construct(array|object $data = [])
├─ Accepts array or object data
├─ Initializes protected $data property
└─ Chainable with static factories

Json::parse(string|array|object $input, array $options = [])
├─ Factory method for parsing
├─ Returns new instance (static return type)
└─ Handles both string parsing and array/object wrapping

Json::create()
├─ Factory for empty instances
├─ Returns new static()
└─ Enables subclass flexibility
```

### 8. **Static Methods & Factories**
```
Factory Methods:
├─ Json::parse()         → Create from JSON string or data
├─ Json::create()        → Create empty instance
└─ Various static helpers in Pointer, Patch, MergePatch
```

### 9. **Magic Methods** (Design Consideration)
```
While not currently implemented, the architecture supports:

Potential __get/__set:
- Provide property-like access to nested data
- Example: $json->user->name instead of $json->get('user.name')

Potential __toString():
- Enable implicit string conversion
- Example: echo $json instead of echo $json->toString()

Potential __call/__callStatic:
- Dynamic method routing
- Example: $json->getUser() → $json->get('user')

Current Design Rationale:
- Explicit is better than implicit (Zen of Python principle)
- Type hints clarity without magic
- Better IDE support and code completion
```

### 10. **Iterators & Generators** (Design Consideration)
```
While not currently implemented, the architecture could support:

Iterator Implementation:
- Implement \Iterator for array-like data
- foreach ($json->toArray() as $key => $value)
- Natural data structure iteration

Generator Usage:
- Memory-efficient processing of large datasets
- yield in methods for lazy evaluation
- Event-driven consumption patterns

Current Alternative:
- DataAccessTrait provides: first(), last(), find(), filter()
- Array conversion via toArray() for iteration
- Reduces memory overhead for typical use cases
```

### 11. **Anonymous Classes** (Extensibility Point)
```
The architecture supports dynamic behavior injection:

// Example: Custom validator logic
$validator = new class implements ValidatorInterface {
    public function validate(mixed $data, array|object $schema): bool {
        // Custom validation logic
        return true;
    }
};

// Used in validation workflows
$isValid = $validator->validate($data, $schema);
```

### 12. **Access Modifiers in Practice**
```
Public API Surface (what users directly access):
├─ Json class methods
├─ Interface contracts
├─ Exception classes
└─ Static utility methods

Protected Infrastructure (for extension):
├─ DataAccessTrait
├─ TransformationTrait
└─ Base exception handling

Private Implementation (encapsulated details):
├─ Pointer parsing logic
├─ Path evaluation logic
├─ Validation recursion
├─ XML transformation helpers
└─ Array flattening/unflattening
```

---

## Layered Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    CONSUMER LAYER                           │
│                  (Application Code)                         │
└─────────────────────────────────────────────────────────────┘
                           │
┌─────────────────────────────────────────────────────────────┐
│                    FACADE LAYER                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │  Json Class (implements JsonInterface)              │   │
│  │  - uses DataAccessTrait                             │   │
│  │  - uses TransformationTrait                         │   │
│  │  - delegates to operation classes                   │   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                           │
┌─────────────────────────────────────────────────────────────┐
│                  CONTRACT LAYER (Interfaces)                │
│  ┌────────────────┐ ┌────────────────┐ ┌──────────────────┐ │
│  │ JsonInterface  │ │PointerInterface│ │ PatchInterface   │ │
│  └────────────────┘ └────────────────┘ └──────────────────┘ │
│  ┌────────────────┐ ┌────────────────┐ ┌──────────────────┐ │
│  │ValidatorInt.   │ │SerializerInt.  │ │TransformerInt.   │ │
│  └────────────────┘ └────────────────┘ └──────────────────┘ │
│  ┌──────────────────────────────────────────────────────┐   │
│  │        HtmlConverterInterface                        │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                           │
┌─────────────────────────────────────────────────────────────┐
│                  TRAIT LAYER (Behaviors)                    │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ DataAccessTrait: get, set, filter, map, reduce...  │   │
│  └─────────────────────────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ TransformationTrait: toXml, toYaml, toCsv, etc.    │   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                           │
┌─────────────────────────────────────────────────────────────┐
│              OPERATION LAYER (Implementations)              │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────────┐   │
│  │ Serializer   │ │ Pointer      │ │ Patch            │   │
│  └──────────────┘ └──────────────┘ └──────────────────┘   │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────────┐   │
│  │ MergePatch   │ │ Path         │ │ Transformer      │   │
│  └──────────────┘ └──────────────┘ └──────────────────┘   │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────────┐   │
│  │ Validator    │ │ HtmlConverter│ │ Minifier         │   │
│  └──────────────┘ └──────────────┘ └──────────────────┘   │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ SchemaGenerator                                      │   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                           │
┌─────────────────────────────────────────────────────────────┐
│                SCHEMA LAYER                                 │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ Schema/Validator: Comprehensive schema validation   │   │
│  │ with caching and error reporting                    │   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                           │
┌─────────────────────────────────────────────────────────────┐
│              UTILITY LAYER (Helpers)                        │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────────┐   │
│  │ JsonPath     │ │ JsonPointer  │ │ JsonMerge        │   │
│  └──────────────┘ └──────────────┘ └──────────────────┘   │
│  ┌──────────────┐ ┌──────────────────────────────────┐   │
│  │ HtmlConverter│ │ Json5Handler                     │   │
│  └──────────────┘ └──────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                           │
┌─────────────────────────────────────────────────────────────┐
│              EXCEPTION LAYER (Error Hierarchy)              │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ JsonException (Base)                                │   │
│  │ ├─ ParseException                                   │   │
│  │ ├─ ValidationException                              │   │
│  │ ├─ PathException                                    │   │
│  │ ├─ TransformException                               │   │
│  │ ├─ RuntimeException                                 │   │
│  │ ├─ IOException                                      │   │
│  │ └─ InvalidArgumentException                         │   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                           │
┌─────────────────────────────────────────────────────────────┐
│              EXTERNAL DEPENDENCIES                          │
│  ├─ symfony/yaml (YAML handling)                           │
│  ├─ symfony/dom-crawler (HTML parsing)                      │
│  ├─ symfony/css-selector (CSS selection)                    │
│  ├─ colinodell/json5 (JSON5 parsing)                        │
│  └─ justinrainbow/json-schema (JSON Schema validation)      │
└─────────────────────────────────────────────────────────────┘
```

---

## Component Inventory

### Complete List of 40+ Components

| # | Category | Component | Namespace | Purpose | Key Responsibility |
|---|----------|-----------|-----------|---------|-------------------|
| **FACADE** |
| 1 | Facade | Json | `Skpassegna\Json` | Primary entry point | Data encapsulation, trait composition, operation delegation |
| **CONTRACTS/INTERFACES** |
| 2 | Contract | JsonInterface | `Skpassegna\Json\Contracts` | Main operations contract | Define core JSON API |
| 3 | Contract | PointerInterface | `Skpassegna\Json\Contracts` | JSON Pointer contract | Define RFC 6901 operations |
| 4 | Contract | PatchInterface | `Skpassegna\Json\Contracts` | JSON Patch contract | Define RFC 6902 operations |
| 5 | Contract | ValidatorInterface | `Skpassegna\Json\Contracts` | Validation contract | Define validation strategy |
| 6 | Contract | SerializerInterface | `Skpassegna\Json\Contracts` | Serialization contract | Define encode/decode strategy |
| 7 | Contract | TransformerInterface | `Skpassegna\Json\Contracts` | Transformation contract | Define format conversion strategy |
| 8 | Contract | HtmlConverterInterface | `Skpassegna\Json\Contracts` | HTML conversion contract | Define HTML ingestion strategy |
| **TRAITS** |
| 9 | Trait | DataAccessTrait | `Skpassegna\Json\Traits` | Data access operations | get, set, filter, map, reduce, sort, slice, find, etc. |
| 10 | Trait | TransformationTrait | `Skpassegna\Json\Traits` | Data transformation operations | toXml, toYaml, toCsv, fromHtml, fromJson5, flatten, unflatten |
| **CORE OPERATIONS (JSON Folder)** |
| 11 | Operation | Serializer | `Skpassegna\Json\Json` | JSON encoding/decoding | Serialize/deserialize JSON with error handling |
| 12 | Operation | Pointer | `Skpassegna\Json\Json` | JSON Pointer (RFC 6901) | Get/set/remove/has/create operations via pointers |
| 13 | Operation | Patch | `Skpassegna\Json\Json` | JSON Patch (RFC 6902) | Apply/diff/test JSON Patch operations |
| 14 | Operation | MergePatch | `Skpassegna\Json\Json` | JSON Merge Patch (RFC 7396) | Apply/generate merge patches |
| 15 | Operation | Path | `Skpassegna\Json\Json` | JSONPath queries | Query JSON using JSONPath expressions |
| 16 | Operation | Transformer | `Skpassegna\Json\Json` | Format transformations | Convert JSON to XML, YAML, CSV |
| 17 | Operation | HtmlConverter | `Skpassegna\Json\Json` | HTML to JSON | Advanced HTML parsing and JSON conversion |
| 18 | Operation | Minifier | `Skpassegna\Json\Json` | JSON minification | Remove whitespace from JSON |
| 19 | Operation | Validator | `Skpassegna\Json\Json` | JSON validation layer | Lightweight validation wrapper |
| 20 | Operation | SchemaGenerator | `Skpassegna\Json\Json` | Schema generation | Generate JSON Schema from PHP objects |
| **SCHEMA OPERATIONS** |
| 21 | Schema | Validator | `Skpassegna\Json\Schema` | Comprehensive validation | JSON Schema validation with caching and error reporting |
| **UTILITIES (Utils Folder)** |
| 22 | Utility | JsonPath | `Skpassegna\Json\Utils` | JSONPath helper | Parse and evaluate JSONPath expressions |
| 23 | Utility | JsonPointer | `Skpassegna\Json\Utils` | JSON Pointer helper | Static methods for pointer operations |
| 24 | Utility | JsonMerge | `Skpassegna\Json\Utils` | Merge helper | Merge JSON data with various strategies |
| 25 | Utility | HtmlConverter | `Skpassegna\Json\Utils` | HTML conversion helper | Convert HTML to JSON with options |
| 26 | Utility | Json5Handler | `Skpassegna\Json\Utils` | JSON5 parser | Parse JSON5 with comment extraction |
| **EXCEPTIONS** |
| 27 | Exception | JsonException | `Skpassegna\Json\Exceptions` | Base exception | Root exception class for all JSON-related errors |
| 28 | Exception | ParseException | `Skpassegna\Json\Exceptions` | Parsing errors | Thrown on malformed JSON input |
| 29 | Exception | ValidationException | `Skpassegna\Json\Exceptions` | Validation errors | Thrown on schema validation failure with error details |
| 30 | Exception | PathException | `Skpassegna\Json\Exceptions` | Path errors | Thrown on invalid or nonexistent path access |
| 31 | Exception | TransformException | `Skpassegna\Json\Exceptions` | Transformation errors | Thrown on format conversion failure |
| 32 | Exception | RuntimeException | `Skpassegna\Json\Exceptions` | Runtime errors | Thrown on generic runtime failures |
| 33 | Exception | IOException | `Skpassegna\Json\Exceptions` | IO errors | Thrown on file/stream operation failures |
| 34 | Exception | InvalidArgumentException | `Skpassegna\Json\Exceptions` | Invalid arguments | Thrown on invalid parameter values |

### Value Objects & Data Structures

| # | Type | Name | Purpose |
|---|------|------|---------|
| 35 | Property | `$data` (Json) | Encapsulated JSON data as array\|object |
| 36 | Property | `$errors` (ValidationException) | Array of validation error details |
| 37 | Property | `$cache` (Path) | Cached parsed path segments for performance |
| 38 | Property | `$schemaCache` (Schema/Validator) | Cached compiled schemas for reuse |

### Constants & Enumerations

| # | Type | Name | Location | Purpose |
|---|------|------|----------|---------|
| 39 | Strategy Constant | `MERGE_RECURSIVE` | `JsonMerge` | Recursive merge strategy identifier |
| 40 | Type Constant | Various JSON flags | Built-in PHP | JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, etc. |

---

## Inheritance & Type Hierarchies

### Exception Hierarchy (IS-A Relationships)

```
┌────────────────────────────┐
│      \Throwable            │
└────────────┬───────────────┘
             │
┌────────────────────────────┐
│      \Exception            │
└────────┬───────────────────┘
         │
┌────────────────────────────────────────────────┐
│  JsonException                                 │
│  (src/Exceptions/JsonException.php)            │
│  └─ Base exception for all JSON operations    │
└────────┬──────────────────────────────────────┘
         │
    ┌────┴─────┬──────────┬────────────┬──────────┬──────────┬─────────────┐
    │           │          │            │          │          │             │
┌───┴─────┐ ┌──┴──────┐ ┌─┴──────────┐┌──┴────┐┌──┴──────┐┌─┴──────────┐┌─┴─────────────┐
│ParseEx  │ │PathEx   │ │ValidationEx││TransEx││RuntimeEx││IOEx        ││InvalidArgEx   │
│         │ │         │ │            ││       ││         ││            ││               │
│ Parsing │ │ Pointer │ │ Schema     ││Format ││Generic  ││ File/      ││ Invalid       │
│ Errors  │ │& Path   │ │ Validation││Conver ││Runtime  ││ Stream     ││ Parameters    │
│         │ │ Errors  │ │ Failures   ││Errors ││Failures ││ Errors     ││               │
└─────────┘ └────────┘ └───────────┘└───────┘└────────┘└────────────┘└───────────────┘

ValidationException extends JsonException with:
├─ public array $errors      (validation error details)
└─ public function getValidationErrors(): array

All exceptions support:
├─ Standard Exception::getMessage()
├─ Standard Exception::getCode()
├─ Standard Exception::getPrevious() (exception chaining)
└─ Standard Exception::getTraceAsString()
```

### Interface Implementation Matrix

```
JsonInterface
├─ Implemented by: Json (main facade)
├─ Methods: parse(), create(), getData(), get(), set(), remove(), 
│            has(), validateSchema(), toString(), query(), merge()
└─ Traits used: DataAccessTrait, TransformationTrait

PointerInterface
├─ Implemented by: Pointer (Json namespace)
├─ Methods: get(), set(), remove(), has(), create()
└─ Used by: Patch, MergePatch for pointer-based access

PatchInterface
├─ Implemented by: Patch (Json namespace)
├─ Methods: apply(), diff(), test()
└─ Standards: RFC 6902 compliant

ValidatorInterface
├─ Implemented by: Validator (Schema namespace)
├─ Methods: validate() (implied in signature)
└─ Returns: bool (true if valid, false if invalid)

SerializerInterface
├─ Implemented by: Serializer (Json namespace)
├─ Methods: serialize(), deserialize()
└─ Delegates to: native PHP json_encode/json_decode

TransformerInterface
├─ Implemented by: Transformer (Json namespace)
├─ Methods: toXml(), toYaml(), toCsv()
└─ Used by: TransformationTrait and Json facade

HtmlConverterInterface
├─ Implemented by: HtmlConverter (Json namespace)
├─ Methods: toArray(), toJson() (implied)
└─ Uses: DomCrawler for parsing
```

### Type Relationships

```
Array<string, mixed>       Used for: Options, configuration, schema
├─ Encoding options: JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES
├─ Parse options: sanitize, max_depth, max_length
└─ HTML options: excludeTags, preserveWhitespace, transformTags

array|object               Used for: Data containers, documents
├─ Internal representation: array (primary)
├─ Alternative form: object (stdClass or custom)
└─ Conversion: toArray(), toObject() methods

string|array|object        Used for: Flexible input acceptance
├─ Json::parse(string|array|object): constructor-like parsing
├─ Pointer operations: string JSON or array representation
└─ Patch operations: string patch or array of operations

mixed                      Used for: Generic data values
├─ Path query results: can be any JSON value
├─ Reducer callbacks: accumulator can be any type
└─ Transform callbacks: result can be any type

static                     Used for: Constructor return types
├─ Json::parse() returns static (enables subclassing)
├─ Json::create() returns static
└─ Trait methods return static (immutable variants)

bool                       Used for: Existence checks, validation results
├─ has(), validateSchema() methods
├─ Validator::validate() returns bool
└─ Patch::test() returns bool
```

---

## Trait Usage & Composition

### DataAccessTrait - Data Access & Collection Operations

**Location**: `src/Traits/DataAccessTrait.php`

**Composed Into**: Json class (via `use DataAccessTrait;`)

**Methods Provided**:

| Method | Signature | Purpose | Returns |
|--------|-----------|---------|---------|
| `keys()` | `(): array<string\|int>` | Get all keys/indices | Array of keys |
| `values()` | `(): array` | Get all values | Array of values |
| `count()` | `(): int` | Count elements | Integer count |
| `isEmpty()` | `(): bool` | Check if empty | Boolean |
| `toArray()` | `(): array` | Convert to array | Array representation |
| `toObject()` | `(): object` | Convert to object | stdClass object |
| `filter()` | `(callable): static` | Filter by callback | New instance (immutable) |
| `map()` | `(callable): static` | Transform via callback | New instance (immutable) |
| `reduce()` | `(callable, mixed): mixed` | Reduce to single value | Accumulated value |
| `sort()` | `(?callable): static` | Sort with optional comparator | New instance (immutable) |
| `slice()` | `(int, ?int): static` | Extract slice | New instance (immutable) |
| `find()` | `(callable): array` | Find matching elements | Array of matches |
| `first()` | `(?callable): mixed` | Get first/first match | First element or null |
| `last()` | `(?callable): mixed` | Get last/last match | Last element or null |
| `get()` | `(string, mixed): mixed` | Get by dot-notation path | Value or default |
| `set()` | `(string, mixed): static` | Set by dot-notation path | Self (mutating) |

**Design Pattern**: Trait composition for horizontal code reuse

**Visibility Model**:
- Public: All user-facing methods
- Private: Path parsing, traversal logic

**State Management**:
- Reads `$this->data` (must be array or object)
- Immutable variants return cloned instances
- Mutating methods return `$this` for chaining

### TransformationTrait - Data Transformation & Format Conversion

**Location**: `src/Traits/TransformationTrait.php`

**Composed Into**: Json class (via `use TransformationTrait;`)

**Methods Provided**:

| Method | Signature | Purpose | Returns |
|--------|-----------|---------|---------|
| `toXml()` | `(string): string` | Convert to XML | XML string |
| `toYaml()` | `(int, int): string` | Convert to YAML | YAML string |
| `toCsv()` | `(array, string, string, string): string` | Convert to CSV | CSV string |
| `fromHtml()` | `(string, array): static` | Ingest HTML | New instance |
| `fromJson5()` | `(string): static` | Parse JSON5 | New instance |
| `prettyPrint()` | `(int): string` | Pretty JSON | Formatted string |
| `minify()` | `(): string` | Minify JSON | Compact string |
| `transform()` | `(callable): static` | Apply callback | New instance (immutable) |
| `flatten()` | `(): static` | Flatten to dot-notation | New instance (immutable) |
| `unflatten()` | `(string): static` | Expand dot-notation | New instance (immutable) |

**Design Pattern**: Trait composition for cross-cutting transformation logic

**Visibility Model**:
- Public: All transformation methods
- Private: XML helper, HTML parsing, array flattening/unflattening

**Dependency Injection**:
- Uses `Symfony\Component\Yaml\Yaml` for YAML conversion
- Uses `Symfony\Component\DomCrawler\Crawler` for HTML parsing
- Uses `ColinODell\Json5\Json5` for JSON5 parsing
- Creates `DOMDocument` for XML pretty-printing

**Error Handling**:
- All methods throw `TransformException` on failure
- Previous exception preserved for debugging

### Trait Composition Example

```php
// In Json.php
class Json implements JsonInterface
{
    use DataAccessTrait;          // Horizontal composition #1
    use TransformationTrait;      // Horizontal composition #2
    
    protected array|object $data = [];
    
    // Instance methods
    public function __construct(array|object $data = []) { ... }
    
    // Static factories
    public static function parse(...) { ... }
    public static function create() { ... }
    
    // Combined interface + trait methods available to consumer
    // DataAccessTrait methods: get(), set(), filter(), map(), etc.
    // TransformationTrait methods: toXml(), toYaml(), toCsv(), etc.
}

// Usage enables fluent chaining
$json = Json::parse('{"users": [...]}')
    ->set('updated', now())           // From DataAccessTrait
    ->filter($fn)                      // From DataAccessTrait
    ->toXml('export');                 // From TransformationTrait
```

---

## Interface Contracts

### JsonInterface

**Purpose**: Define the primary JSON operations contract

**Location**: `src/Contracts/JsonInterface.php`

**Methods**:
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

**Implementations**: Json (main facade)

**Consumers**: Any code expecting JSON interface compliance

### PointerInterface

**Purpose**: Define RFC 6901 JSON Pointer operations contract

**Location**: `src/Contracts/PointerInterface.php`

**Methods**:
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

**Implementations**: Pointer (Json namespace)

**Standards**: RFC 6901 compliant

**Usage Pattern**:
```php
$pointer = new Pointer();
$value = $pointer->get($document, '/path/to/value');
$updated = $pointer->set($document, '/path/to/value', $newValue);
```

### PatchInterface

**Purpose**: Define RFC 6902 JSON Patch operations contract

**Location**: `src/Contracts/PatchInterface.php`

**Methods**:
```php
interface PatchInterface
{
    public function apply(string|array $document, string|array $patch, bool $mutate = false): string|array;
    public function diff(string|array $source, string|array $target): string;
    public function test(string|array $document, string|array $patch): bool;
}
```

**Implementations**: Patch (Json namespace)

**Standards**: RFC 6902 compliant

**Operations Supported**:
- `add`: Add value at path
- `remove`: Remove value at path
- `replace`: Replace value at path
- `move`: Move value from path to another path
- `copy`: Copy value from path to another path
- `test`: Test that value at path matches

### ValidatorInterface

**Purpose**: Define schema validation contract

**Location**: `src/Contracts/ValidatorInterface.php`

**Methods** (implied by implementation):
```php
interface ValidatorInterface
{
    public function validate(mixed $data, array|object $schema): bool;
    public function getErrors(): array;
}
```

**Implementations**: Validator (Schema namespace)

**Features**:
- Schema caching for performance
- Comprehensive error reporting
- Type validation
- Property validation
- Array item validation
- String length constraints
- Numeric constraints
- Custom format validation

### SerializerInterface

**Purpose**: Define JSON serialization contract

**Location**: `src/Contracts/SerializerInterface.php`

**Methods**:
```php
interface SerializerInterface
{
    public function serialize(mixed $data, int $flags = 0, int $depth = 512): string;
    public function deserialize(string $json, bool $associative = false, int $depth = 512, int $flags = 0): mixed;
}
```

**Implementations**: Serializer (Json namespace)

**Features**:
- Wraps native PHP `json_encode()` and `json_decode()`
- Error handling with `RuntimeException` on failure
- Support for all JSON encoding options
- Exception chaining for debugging

### TransformerInterface

**Purpose**: Define format transformation contract

**Location**: `src/Contracts/TransformerInterface.php`

**Methods**:
```php
interface TransformerInterface
{
    public function toXml(string $json): string;
    public function toYaml(string $json): string;
    public function toCsv(string $json, string $delimiter = ',', string $enclosure = '"', string $escape = '\\'): string;
}
```

**Implementations**: Transformer (Json namespace)

**Supported Formats**:
- XML (via `SimpleXMLElement` and `DOMDocument`)
- YAML (via Symfony Yaml component)
- CSV (via PHP `fputcsv()`)

### HtmlConverterInterface

**Purpose**: Define HTML to JSON conversion contract

**Location**: `src/Contracts/HtmlConverterInterface.php`

**Methods** (implied):
```php
interface HtmlConverterInterface
{
    public function toArray(string $html, array $options = []): array;
    public function toJson(string $html, array $options = []): string;
}
```

**Implementations**: HtmlConverter (Json namespace)

**Features**:
- HTML structure preservation
- Attribute extraction
- Tag filtering
- Custom tag transformation
- Whitespace handling options

---

## Exception Hierarchy

### Exception Class Definitions

#### JsonException (Base)

**Location**: `src/Exceptions/JsonException.php`

**Purpose**: Root exception for all JSON-related errors

**Definition**:
```php
class JsonException extends Exception
{
    // Inherits from \Exception
    // Standard properties: message, code, file, line
    // Standard methods: getMessage(), getCode(), getFile(), getLine(), getTrace(), etc.
}
```

**Usage Pattern**: Catch-all for all JSON operation errors

#### ParseException

**Location**: `src/Exceptions/ParseException.php`

**Purpose**: Thrown when JSON parsing fails

**Extends**: JsonException

**Triggered By**:
- Malformed JSON in `Json::parse()`
- Invalid JSON in `Serializer::deserialize()`
- Invalid JSON Pointer in path operations
- Malformed patch operations

**Example**:
```php
try {
    $json = Json::parse('invalid json');
} catch (ParseException $e) {
    echo "Parse failed: " . $e->getMessage();
}
```

#### ValidationException

**Location**: `src/Exceptions/ValidationException.php`

**Purpose**: Thrown when schema validation fails

**Extends**: JsonException

**Properties**:
```php
private array $errors;  // Validation error details
```

**Methods**:
```php
public function __construct(string $message, array $errors = [], int $code = 0, ?Throwable $previous = null)
public function getValidationErrors(): array
```

**Usage Pattern**:
```php
try {
    $json->validateSchema($schema);
} catch (ValidationException $e) {
    $errors = $e->getValidationErrors();
    foreach ($errors as $error) {
        echo "Validation error: " . $error;
    }
}
```

#### PathException

**Location**: `src/Exceptions/PathException.php`

**Purpose**: Thrown when path access fails

**Extends**: JsonException

**Triggered By**:
- Invalid JSON Pointer format
- Nonexistent path in pointer operations
- Invalid JSONPath expression
- Path traversal errors

**Example**:
```php
try {
    $value = Json::parse($data)->getPointer('/invalid/path');
} catch (PathException $e) {
    echo "Path error: " . $e->getMessage();
}
```

#### TransformException

**Location**: `src/Exceptions/TransformException.php`

**Purpose**: Thrown when format transformation fails

**Extends**: JsonException

**Triggered By**:
- XML conversion errors
- YAML conversion errors
- CSV conversion errors
- HTML parsing errors
- JSON5 parsing errors

**Example**:
```php
try {
    $xml = $json->toXml();
} catch (TransformException $e) {
    echo "Transform failed: " . $e->getMessage();
}
```

#### RuntimeException

**Location**: `src/Exceptions/RuntimeException.php`

**Purpose**: Thrown on generic runtime errors

**Extends**: JsonException

**Triggered By**:
- Operation failures not covered by specific exceptions
- Internal logic errors
- Serialization errors
- Query evaluation errors

**Definition**:
```php
class RuntimeException extends JsonException
{
    public function __construct(
        string $message,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
```

#### IOException

**Location**: `src/Exceptions/IOException.php`

**Purpose**: Thrown on file/stream operation errors

**Extends**: JsonException

**Triggered By**:
- File read/write failures
- Stream operation errors
- CSV output stream errors

**Definition**:
```php
class IOException extends JsonException
{
    public function __construct(
        string $message,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
```

#### InvalidArgumentException

**Location**: `src/Exceptions/InvalidArgumentException.php`

**Purpose**: Thrown when invalid arguments are provided

**Extends**: JsonException

**Triggered By**:
- Invalid parameter types
- Out-of-range values
- Invalid option values
- Null when value required

**Definition**:
```php
class InvalidArgumentException extends JsonException
{
    public function __construct(
        string $message,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
```

### Exception Handling Strategy

```
Granular Exception Handling:
├─ Catch specific exceptions for recovery attempts
│  ├─ ParseException → handle malformed input
│  ├─ ValidationException → report validation details
│  ├─ PathException → handle missing paths
│  └─ TransformException → retry with different format
└─ Catch JsonException for unified error handling
   └─ Log and re-throw or present user-friendly error

Exception Chaining:
├─ All exceptions preserve previous exception via $previous parameter
├─ Enables full stack trace for debugging
└─ Supports nested try-catch-throw patterns

Error Context Preservation:
├─ ValidationException includes $errors array
├─ Other exceptions preserve original exception message
└─ All exceptions support standard Exception methods
```

---

## Design Patterns

### 1. Facade Pattern

**Implementation**: Json class

**Purpose**: Simplify complex subsystem for consumers

**Structure**:
```
Consumer → Json (Facade) → Traits → Operation Classes → Utilities
```

**Example**:
```php
// Facade hides complexity
$json = Json::parse($data)
    ->set('key', 'value')      // DataAccessTrait
    ->toXml();                  // TransformationTrait

// Actual path:
// Json::set() → DataAccessTrait::set()
// Json::toXml() → TransformationTrait::toXml() → Transformer class
```

### 2. Strategy Pattern

**Implementation**: Interface-based operation classes

**Purpose**: Allow interchangeable algorithms

**Structure**:
```
Interface ←→ Multiple implementations (strategies)
├─ SerializerInterface → Serializer
├─ TransformerInterface → Transformer
├─ ValidatorInterface → multiple implementations
└─ etc.
```

**Benefit**: Different strategies can be swapped without consumer code changes

### 3. Factory Pattern

**Implementation**: Static methods in Json and operation classes

**Purpose**: Encapsulate object creation

**Methods**:
```php
Json::parse($input)           // Factory for parsing
Json::create()                // Factory for empty instance
Pointer::create($segments)    // Factory for pointer strings
Patch::apply(...)             // Factory-like creation of patches
```

### 4. Adapter Pattern

**Implementation**: Utils classes wrap external dependencies

**Purpose**: Convert interface of classes for compatibility

**Examples**:
```php
Json5Handler::decode()       // Adapts ColinODell\Json5 to our API
HtmlConverter (Utils)         // Adapts DomCrawler to our API
Transformer::toYaml()        // Adapts Symfony Yaml to our API
```

### 5. Trait Composition (Mixin Pattern)

**Implementation**: DataAccessTrait and TransformationTrait

**Purpose**: Provide reusable behaviors without deep inheritance

**Benefit**:
- Avoids multiple inheritance limitations
- Cleanly composes behaviors
- Enables independent evolution of traits
- Clear separation of concerns

### 6. Immutable Data Pattern

**Implementation**: Methods returning `static` (new instances)

**Purpose**: Enable functional-style data operations

**Methods**:
```php
$original = Json::parse($data);
$filtered = $original->filter($fn);    // New instance, $original unchanged
$mapped = $original->map($fn);         // New instance
$cloned = clone $original;             // Explicit cloning
```

**Variants**:
```php
// Mutating variant (returns $this for chaining)
$json->set('key', 'value');            // Modifies in place

// Immutable variant (returns new instance via trait)
$filtered = $json->filter($fn);        // Returns new instance
```

### 7. Observer/Hook Pattern (Potential)

**Extensibility Point**: Anonymous classes for custom behavior

**Example**:
```php
$customValidator = new class implements ValidatorInterface {
    public function validate(mixed $data, array|object $schema): bool {
        // Custom validation logic
        return true;
    }
};
```

### 8. Template Method Pattern (Potential)

**In Validators**: Common structure with specialized type checking

```php
// Pseudo-code showing pattern
class Validator {
    private function validateValue($value, $schema) {
        // Template: check type
        $this->validateType($value, $schema['type']);
        // Template: check properties
        if (isset($schema['properties'])) {
            $this->validateProperties($value, $schema['properties']);
        }
        // Template: check items (specialized step)
        if (isset($schema['items'])) {
            $this->validateItems($value, $schema['items']);
        }
    }
}
```

### 9. Iterator Pattern (Design Consideration)

**Current Alternative**: Array conversion + native foreach

**Potential Enhancement**:
```php
class JsonIterator implements Iterator {
    public function current(): mixed { ... }
    public function key(): mixed { ... }
    public function next(): void { ... }
    public function rewind(): void { ... }
    public function valid(): bool { ... }
}
```

### 10. Visitor Pattern (Design Consideration)

**For Recursive Operations**: Could encapsulate tree traversal

**Example Use Case**:
- Schema validation visitor
- Path query visitor
- Data transformation visitor

---

## Directory Structure & Mapping

### Source Code Organization

```
src/
├── Json.php                          (Main Facade)
│   ├── Implements: JsonInterface
│   ├── Uses: DataAccessTrait, TransformationTrait
│   └── Delegates to: Pointer, Patch, MergePatch, Path, etc.
│
├── Contracts/                        (Interface Definitions)
│   ├── JsonInterface.php             (Main contract)
│   ├── PointerInterface.php          (RFC 6901)
│   ├── PatchInterface.php            (RFC 6902)
│   ├── ValidatorInterface.php        (Validation)
│   ├── SerializerInterface.php       (Serialization)
│   ├── TransformerInterface.php      (Format conversion)
│   └── HtmlConverterInterface.php    (HTML ingestion)
│
├── Exceptions/                       (Error Hierarchy)
│   ├── JsonException.php             (Base exception)
│   ├── ParseException.php            (Parsing errors)
│   ├── ValidationException.php       (Validation errors)
│   ├── PathException.php             (Path errors)
│   ├── TransformException.php        (Transformation errors)
│   ├── RuntimeException.php          (Runtime errors)
│   ├── IOException.php               (IO errors)
│   └── InvalidArgumentException.php  (Invalid arguments)
│
├── Json/                             (Core Operations)
│   ├── Serializer.php                (JSON encode/decode)
│   ├── Pointer.php                   (RFC 6901 implementation)
│   ├── Patch.php                     (RFC 6902 implementation)
│   ├── MergePatch.php                (RFC 7396 implementation)
│   ├── Path.php                      (JSONPath engine)
│   ├── Transformer.php               (Format conversions)
│   ├── HtmlConverter.php             (HTML to JSON)
│   ├── Minifier.php                  (JSON minification)
│   ├── Validator.php                 (JSON validation layer)
│   └── SchemaGenerator.php           (Schema generation)
│
├── Schema/                           (Schema Operations)
│   └── Validator.php                 (Comprehensive validation)
│
├── Traits/                           (Behavior Composition)
│   ├── DataAccessTrait.php           (Data access + collection)
│   └── TransformationTrait.php       (Format transformations)
│
└── Utils/                            (Utility Helpers)
    ├── JsonPath.php                  (JSONPath helper)
    ├── JsonPointer.php               (Pointer helper)
    ├── JsonMerge.php                 (Merge helper)
    ├── HtmlConverter.php             (HTML conversion helper)
    └── Json5Handler.php              (JSON5 parser)
```

### Namespace Organization

```
Skpassegna\Json\
├── Json                              (Main facade class)
│
├── Contracts\
│   ├── JsonInterface
│   ├── PointerInterface
│   ├── PatchInterface
│   ├── ValidatorInterface
│   ├── SerializerInterface
│   ├── TransformerInterface
│   └── HtmlConverterInterface
│
├── Exceptions\
│   ├── JsonException
│   ├── ParseException
│   ├── ValidationException
│   ├── PathException
│   ├── TransformException
│   ├── RuntimeException
│   ├── IOException
│   └── InvalidArgumentException
│
├── Json\
│   ├── Serializer
│   ├── Pointer
│   ├── Patch
│   ├── MergePatch
│   ├── Path
│   ├── Transformer
│   ├── HtmlConverter
│   ├── Minifier
│   ├── Validator
│   └── SchemaGenerator
│
├── Schema\
│   └── Validator
│
├── Traits\
│   ├── DataAccessTrait
│   └── TransformationTrait
│
└── Utils\
    ├── JsonPath
    ├── JsonPointer
    ├── JsonMerge
    ├── HtmlConverter
    └── Json5Handler
```

### Dependency Flow

```
Direction of Dependencies (Layers):
Facade → Traits → Operations → Utils → External Dependencies

Detailed Flow:
┌─────────────────────────────────────────────────────────┐
│ Consumer Application Code                               │
└─────────────────────────┬───────────────────────────────┘
                          │
┌─────────────────────────┴───────────────────────────────┐
│ Json Facade (Primary Interaction Point)                 │
│ ├─ New constructor(data)                                │
│ ├─ Static parse(input)                                  │
│ ├─ Static create()                                      │
│ └─ Methods from traits and delegations                  │
└────────┬────────────────────────────┬──────────────────┘
         │                            │
         ▼                            ▼
    Traits Layer            Operation Classes
    ├─ DataAccessTrait      ├─ Serializer
    └─ TransformationTrait  ├─ Pointer
                            ├─ Patch
                            ├─ MergePatch
                            ├─ Path
                            ├─ Transformer
                            ├─ Validator
                            ├─ HtmlConverter
                            └─ etc.
         │                            │
         └────────────┬───────────────┘
                      │
         ┌────────────┴─────────────┐
         │                          │
         ▼                          ▼
    Utility Helpers          Schema Validator
    ├─ JsonPath              └─ Schema/Validator
    ├─ JsonPointer
    ├─ JsonMerge
    ├─ HtmlConverter
    └─ Json5Handler
         │                          │
         └────────────┬─────────────┘
                      │
    ┌─────────────────┴───────────────────┐
    │  External Dependencies              │
    ├─ symfony/yaml                       │
    ├─ symfony/dom-crawler                │
    ├─ symfony/css-selector               │
    ├─ colinodell/json5                   │
    ├─ justinrainbow/json-schema          │
    └─ PHP Built-in (json_*, DOM, etc.)   │
```

### Implementation Context

Each component in the source tree serves specific purposes aligned with layered architecture:

**Facade Layer** (`src/Json.php`)
- Entry point for all JSON operations
- Composes traits for behavior
- Delegates to specialized operations
- Maintains single `$data` property

**Contract Layer** (`src/Contracts/`)
- Defines interfaces for implementations
- Enables polymorphism and testability
- Segregates concerns via focused APIs
- Implementation-agnostic

**Operation Layer** (`src/Json/`)
- Concrete implementations of contracts
- Single responsibility per class
- Specialized algorithms (patching, querying, etc.)
- RFC standard compliance

**Schema Layer** (`src/Schema/`)
- Advanced validation with caching
- Error reporting and accumulation
- Schema compilation and optimization
- Separate from lightweight validator in Json/

**Utility Layer** (`src/Utils/`)
- Static helper methods
- No state/instance required
- Common operations extraction
- Reusable across components

**Trait Layer** (`src/Traits/`)
- Horizontal code reuse
- Composed into facade
- Avoids deep inheritance chains
- Independent evolution possible

**Exception Layer** (`src/Exceptions/`)
- Hierarchical error taxonomy
- Granular catch strategies
- Error context preservation
- Type-safe error handling

---

## Future Enhancement Points

### 1. Iterator Support
```php
// Enable natural iteration
foreach ($json->getData() as $key => $value) {
    // Process items
}
```

### 2. Magic Methods
```php
// Property-like access
$json->user->email                  // Instead of $json->get('user.email')

// Implicit string conversion
echo $json                           // Instead of $json->toString()
```

### 3. Additional Standards Compliance
- JSON Schema Draft 2020-12 (currently partial)
- JSON Lines format support
- CBOR (Concise Binary Object Representation)

### 4. Streaming Support
```php
// Memory-efficient large file handling
$stream = new JsonStream('large.json');
foreach ($stream as $item) {
    // Process item by item
}
```

### 5. Query Language Extensions
- Enhanced filter expressions
- Complex predicates in JSONPath
- JQL (JSON Query Language) support

### 6. Performance Optimizations
- Query result caching
- Schema compilation caching
- Lazy evaluation support
- Parallel processing capability

---

## Conclusion

This specification documents a comprehensive, modular JSON management library built with advanced PHP 8+ object-oriented principles. The 40+ components are organized in layered architecture enabling clear separation of concerns, extensibility through contracts, and maintainability through trait composition. All PHP OOP concepts (encapsulation, abstraction, inheritance, polymorphism, traits, type system features) are exercised throughout the codebase, providing a reference implementation for professional PHP package design.

---

**Document Version**: 1.0  
**Last Reviewed**: November 28, 2025  
**Status**: Draft Architecture Specification
