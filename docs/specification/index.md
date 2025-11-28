# Specification Index

Welcome to the JSON Parser Library Specification documentation. This directory contains comprehensive architecture and design documentation for the library.

## Available Documents

### [Architecture Specification](./architecture.md)

**Comprehensive Blueprint** of the JSON Parser Library architecture covering:

- **Executive Summary** - Overview and key architectural objectives
- **Architectural Principles** - Design philosophy and core tenets
- **Core Components Overview** - All major component categories
- **PHP OOP Concepts Application** - Detailed mapping of PHP 8+ features to codebase
  - Encapsulation (access modifiers)
  - Abstraction (interfaces)
  - Inheritance (exception hierarchy)
  - Polymorphism (multiple implementations)
  - Traits (horizontal composition)
  - Type system (union types, named arguments, strict typing)
  - Constructors & initialization
  - Static methods & factories
  - Magic methods (design considerations)
  - Iterators & generators (extensibility points)
  - Anonymous classes (dynamic behavior)
  - Access modifiers in practice

- **Layered Architecture** - Visual representation and explanation of all 7 layers
  - Consumer Layer
  - Facade Layer
  - Contract Layer
  - Trait Layer
  - Operation Layer
  - Schema Layer
  - Utility Layer
  - Exception Layer
  - External Dependencies

- **Component Inventory** - Complete list of 40+ components including:
  - 1 Facade
  - 7 Contracts/Interfaces
  - 2 Traits
  - 11 Core Operations
  - 1 Schema Operation
  - 5 Utilities
  - 8 Exceptions
  - 4 Value Objects & Data Structures
  - 2 Constants & Enumerations

- **Inheritance & Type Hierarchies** - Complete diagrams showing:
  - Exception hierarchy with all 8 exception types
  - Interface implementation matrix
  - Type relationships and union types

- **Trait Usage & Composition** - Detailed breakdown of:
  - DataAccessTrait (15 methods)
  - TransformationTrait (11 methods)
  - Composition patterns and usage examples

- **Interface Contracts** - Complete definition of all 7 interfaces:
  - JsonInterface
  - PointerInterface
  - PatchInterface
  - ValidatorInterface
  - SerializerInterface
  - TransformerInterface
  - HtmlConverterInterface

- **Exception Hierarchy** - Complete exception taxonomy with:
  - JsonException (base)
  - ParseException
  - ValidationException
  - PathException
  - TransformException
  - RuntimeException
  - IOException
  - InvalidArgumentException

- **Design Patterns** - 10 patterns used/considered:
  1. Facade Pattern
  2. Strategy Pattern
  3. Factory Pattern
  4. Adapter Pattern
  5. Trait Composition (Mixin)
  6. Immutable Data Pattern
  7. Observer/Hook Pattern
  8. Template Method Pattern
  9. Iterator Pattern (design consideration)
  10. Visitor Pattern (design consideration)

- **Directory Structure & Mapping** - Complete file organization:
  - Source code directory tree
  - Namespace organization
  - Dependency flow
  - Implementation context for each layer

- **Future Enhancement Points** - Planned extensions:
  1. Iterator Support
  2. Magic Methods (__get, __set, __toString)
  3. Additional Standards Compliance
  4. Streaming Support
  5. Query Language Extensions
  6. Performance Optimizations

## Quick Navigation

### For Understanding Component Relationships
Start with the **"Inheritance & Type Hierarchies"** section to visualize how components relate to each other.

### For Learning OOP Implementation
Review the **"PHP OOP Concepts Application"** section for detailed examples of encapsulation, abstraction, polymorphism, and other concepts.

### For Exploring API Contracts
Visit the **"Interface Contracts"** section to understand what each interface provides and its implementation.

### For Understanding Error Handling
Check the **"Exception Hierarchy"** section to learn the exception taxonomy and proper error handling strategies.

### For Understanding Component Organization
See the **"Directory Structure & Mapping"** section to understand how components are organized and their dependencies.

## Component Quick Reference

### Facades & Entry Points
- **Json** (`src/Json.php`) - Main entry point, implements JsonInterface, uses DataAccessTrait and TransformationTrait

### Contracts/Interfaces (7 total)
- **JsonInterface** - Core JSON operations
- **PointerInterface** - JSON Pointer (RFC 6901) operations
- **PatchInterface** - JSON Patch (RFC 6902) operations
- **ValidatorInterface** - Schema validation
- **SerializerInterface** - JSON serialization/deserialization
- **TransformerInterface** - Format transformations (XML, YAML, CSV)
- **HtmlConverterInterface** - HTML to JSON conversion

### Core Operations (11 total)
- **Serializer** - JSON encoding/decoding
- **Pointer** - RFC 6901 implementation
- **Patch** - RFC 6902 implementation
- **MergePatch** - RFC 7396 implementation
- **Path** - JSONPath query engine
- **Transformer** - Format conversions
- **HtmlConverter** - HTML to JSON conversion
- **Minifier** - JSON minification
- **Validator** - JSON validation layer
- **SchemaGenerator** - Schema generation

### Schema Operations
- **Schema/Validator** - Comprehensive schema validation with caching

### Utilities (5 total)
- **JsonPath** - JSONPath helper with expression parsing
- **JsonPointer** - JSON Pointer static helpers
- **JsonMerge** - JSON merging strategies
- **HtmlConverter** - HTML conversion helper
- **Json5Handler** - JSON5 parsing with comment extraction

### Traits (2 total)
- **DataAccessTrait** - Data access and collection operations (15 methods)
- **TransformationTrait** - Data transformation and format conversion (11 methods)

### Exceptions (8 total)
- **JsonException** - Base exception for all JSON-related errors
- **ParseException** - Parsing errors
- **ValidationException** - Schema validation errors
- **PathException** - Path-related errors
- **TransformException** - Format transformation errors
- **RuntimeException** - Generic runtime errors
- **IOException** - File/stream operation errors
- **InvalidArgumentException** - Invalid parameter errors

## Standards Compliance

The library implements the following Internet standards:

- **RFC 6901** - JSON Pointer (implemented in Pointer and Json/Pointer classes)
- **RFC 6902** - JSON Patch (implemented in Patch and Json/Patch classes)
- **RFC 7396** - JSON Merge Patch (implemented in MergePatch and Json/MergePatch classes)

Additional support for:
- JSON Schema (Draft 2020-12, partial)
- JSON5 format
- YAML conversion
- XML conversion
- CSV conversion
- HTML parsing

## Development Context

This specification document serves as the authoritative reference for:

- **Contributors** - Understanding component responsibilities and architecture
- **Maintainers** - Decision-making on component changes and new features
- **Code Reviewers** - Ensuring consistency with architectural principles
- **Users/Consumers** - Understanding what capabilities are available
- **Integrators** - Mapping their needs to available components

## Version Information

- **Specification Version**: 1.0
- **Document Status**: Draft Architecture Specification
- **Last Updated**: 2024
- **Scope**: Covers complete component inventory and architectural design

## Related Resources

- README.md - Quick start and basic usage
- FEATURES.md - Feature descriptions and usage examples
- CONTRIBUTING.md - Guidelines for contributors
- PHP 8+ Documentation - For language features used
- RFC 6901, 6902, 7396 - JSON standards

---

For detailed information on any component or concept, refer to the [Architecture Specification](./architecture.md).
