# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- **Streaming & Caching Framework**:
  - `StreamingJsonParser` with PSR-7 StreamInterface support for non-blocking I/O
  - `StreamingJsonSerializer` for generator-based incremental serialization
  - `LazyJsonProxy` with deferred parsing and ArrayAccess/magic method support
  - `MemoryStore` cache implementation with TTL support and automatic expiration
  - `CacheStoreInterface` for pluggable cache strategies
  - `StreamingBuilder` fluent API for advanced streaming configuration
  - Newline-delimited JSON (NDJSON) parsing and serialization
  - Event callbacks for chunk processing and error handling
  - Sub-linear memory usage for large JSON payloads
  - Query result caching integration via `queryWithCache()`
- **PSR-7 Integration**:
  - Added `psr/http-message` dependency for StreamInterface support
  - Static factory methods: `Json::parseStream()`, `Json::parseNdJsonStream()`, `Json::serializeStream()`, `Json::serializeNdJsonStream()`
  - Fluent builder: `Json::streaming()` for configuration chaining
- **PHP 8.4+ Type System Uplift**:
  - Backed enums: `JsonMergeStrategy`, `NumberFormat`, `TraversalMode`
  - `TypeCoercionService` for type normalization and coercion with strict/lenient modes
  - `TypeCoercionTrait` for opt-in type coercion in classes
  - PHP 8.4 array helper methods: `findElement()`, `findElementKey()`, `anyMatch()`, `allMatch()`, `firstElement()`, `lastElement()`, `lastElementKey()`
  - Key existence checkers: `hasAnyKey()`, `hasAllKeys()`
  - Enhanced `mapWith()` and `filterWith()` with key-aware callable support
  - `ArrayHelpers` utility with polyfills for `array_find`, `array_find_key`, `array_any`, `array_all`
  - Intersection type hints for transformers and validators
- Advanced HTML to JSON conversion with configurable options
- JSON5 support with comment preservation
- Symfony YAML component integration
- Table structure optimization for HTML conversion
- Custom tag transformation support
- Comprehensive test suite with mutation testing
- Performance benchmarks for streaming operations
- Static analysis tools integration
- Automated code style fixes
- Security checks in CI pipeline
- Integration examples in `examples/streaming/` directory

### Changed
- **BREAKING**: Minimum PHP version now 8.0+ (added explicit 8.3|8.4 support)
- Improved error handling with specific exceptions
- Enhanced type safety with PHP 8.0+ union types and nullable types
- Added readonly promoted properties support where appropriate
- Optimized performance for large JSON structures
- Updated documentation with comprehensive examples and new helper methods
- Restructured codebase for better maintainability
- Polyfills for PHP 8.4 array functions on earlier versions

### Fixed
- Memory usage optimization for large datasets
- Proper handling of special characters in HTML
- JSON5 comment extraction edge cases
- YAML conversion with complex structures
- Type coercion edge cases with strict/lenient modes

## [1.0.0] - YYYY-MM-DD

### Added
- Initial release with core JSON parsing features
- JSON Schema validation
- Basic format conversions (XML, YAML, CSV)
- JSONPath support
- JSON Pointer operations
- JSON Patch operations
- Data transformation utilities
- Documentation and examples

[Unreleased]: https://github.com/skpassegna/json-parser/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/skpassegna/json-parser/releases/tag/v1.0.0
