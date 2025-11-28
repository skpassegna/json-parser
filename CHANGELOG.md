# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

### Added
- Core `Json` class for fluent manipulation.
- Dot-notation access for getting, setting, and removing data.
- JSON Pointer (RFC 6902) implementation.
- JSONPath query engine.
- Streaming parser and builder for large datasets and NDJSON support.
- Lazy loading via `LazyJsonProxy`.
- Advanced Merge and Diff strategies (RFC 7396, Recursive, Deep).
- Event Dispatcher (PSR-14 compatible) for lifecycle hooks.
- Transformation utilities for XML, YAML, CSV, HTML, and JSON5.
- Reflection inspector for document statistics and schema inference.
- Procedural helper functions (`json_parse`, `json_get`, etc.).