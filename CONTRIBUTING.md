# Contributing to skpassegna/json-parser

Thank you for considering contributing to this project. Please read the following guidelines to ensure a smooth contribution process.

## Requirements

* PHP >= 8.0
* Composer

## Development Setup

1. Clone the repository.
2. Run `composer install` to install dependencies.

## Testing

This project uses PHPUnit for testing. Please ensure all tests pass before submitting a Pull Request.

```bash
composer test
```

## Static Analysis

We use PHPStan to ensure type safety and code quality.

```bash
composer analyse
```

## Coding Style

We follow PSR-12 coding standards enforced by PHP-CS-Fixer.

```bash
# Check style
composer check-style

# Fix style automatically
composer fix-style
```

## Benchmarking

For performance-critical changes, please run the benchmarks.

```bash
composer benchmark
```

## Pull Requests

1. Create a new branch for your feature or fix.
2. Write tests covering your changes.
3. Ensure the test suite passes locally.
4. Run static analysis and style checks.
5. Submit the Pull Request with a clear description of changes.