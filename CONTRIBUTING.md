# Contributing to skpassegna/json-parser

Thank you for considering contributing to the JSON library! This document provides guidelines and instructions for contributing.

## Code of Conduct

This project and everyone participating in it is governed by our [Code of Conduct](CODE_OF_CONDUCT.md). By participating, you are expected to uphold this code.

## Branch Naming Conventions

- Use `feature/` for new features
- Use `bugfix/` for bug fixes
- Use `hotfix/` for urgent fixes

## Commit Message Guidelines

- Use the present tense ("Add feature" not "Added feature")
- Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
- Limit the first line to 72 characters or less

## Review Process

- All submissions, including submissions by project members, require review
- Follow the project's coding standards
- Ensure all tests pass before submitting

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the issue list as you might find out that you don't need to create one. When you are creating a bug report, please include as many details as possible:

* Use a clear and descriptive title
* Describe the exact steps which reproduce the problem
* Provide specific examples to demonstrate the steps
* Describe the behavior you observed after following the steps
* Explain which behavior you expected to see instead and why
* Include PHP version and library version

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. Create an issue and provide the following information:

* Use a clear and descriptive title
* Provide a step-by-step description of the suggested enhancement
* Provide specific examples to demonstrate the steps
* Describe the current behavior and explain which behavior you expected to see instead
* Explain why this enhancement would be useful

### Pull Requests

* Fill in the required template
* Do not include issue numbers in the PR title
* Follow the PHP coding style (PSR-12)
* Include thoughtfully-worded, well-structured tests
* Document new code
* End all files with a newline

## Development Process

1. Fork the repository
2. Create a new branch for your feature
3. Write your code
4. Write tests for your code
5. Run the test suite
6. Push your branch and submit a pull request

### Setup Development Environment

```bash
# Clone your fork
git clone git@github.com:skpassegna/json-parser-parser.git

# Install dependencies
composer install

# Run tests
composer test

# Run static analysis
composer analyse

# Check code style
composer check-style

# Fix code style
composer fix-style
```

### Coding Standards

* Follow PSR-12 coding style
* Write documentation for all public methods
* Add type hints for parameters and return types
* Use meaningful variable names
* Keep methods focused and concise

### Testing

* Write unit tests for all new code
* Ensure all tests pass before submitting PR
* Aim for high code coverage
* Include both positive and negative test cases

## Dependencies and Tools

- Ensure you have Composer installed
- Use PHPStan for static analysis
- Use PHPUnit for testing

## Local Testing

- Run `composer install` to install dependencies
- Run `vendor/bin/phpunit` to execute tests
- Run `vendor/bin/phpstan analyse` for static analysis

## Documentation

* Use clear and consistent terminology
* Include code examples
* Document all public methods and properties
* Keep documentation up to date with code changes

## Questions?

Feel free to ask questions by creating an issue.
