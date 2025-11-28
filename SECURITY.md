# Security Policy

## Supported Versions

We release patches for security vulnerabilities for the following versions:

| Version | Supported          |
| ------- | ------------------ |
| 1.x.x   | :white_check_mark: |
| < 1.0   | :x:                |

## Reporting a Vulnerability

If you discover a security vulnerability within this package, please send an email to [security@skpassegna.com](mailto:security@skpassegna.com). All security vulnerabilities will be promptly addressed.

Please do not report security vulnerabilities through public GitHub issues.

### Process

1. You submit your vulnerability report via email
2. We will acknowledge receipt of your vulnerability report
3. We will investigate and determine the potential impact
4. We will develop and test a fix
5. We will prepare a security advisory and release a patch
6. The security advisory will be published after the patch is released

### What to Include

Please include the following in your report:

- Description of the vulnerability
- Steps to reproduce the issue
- Potential impact of the vulnerability
- Suggested fix (if possible)
- Your contact information

### Security Tools

We use the following tools for security analysis:
- PHPStan for static analysis
- Composer Security Checker for dependency vulnerabilities

### Dependencies

- Regularly update dependencies to mitigate known vulnerabilities
- Use `composer outdated` to check for outdated packages

## Security Best Practices

When using this library, consider the following security best practices:

### 1. Input Validation & Depth Limits

Always validate and sanitize JSON input before processing, especially from untrusted sources:

```php
use Skpassegna\Json\Json;

// Apply depth and length limits to prevent DoS attacks
$json = Json::parse($untrustedInput, [
    'max_depth' => 10,      // Prevent deeply nested payloads
    'max_length' => 1000000 // Prevent memory exhaustion attacks
]);
```

**Why**: Deeply nested JSON or extremely large payloads can cause:
- Stack overflow attacks
- Memory exhaustion (DoS)
- Processing delays
- Slow hash table collisions (Hash-Flooding attacks)

### 2. Schema Validation

Always validate parsed JSON against a schema before processing:

```php
$schema = [
    'type' => 'object',
    'properties' => [
        'name' => ['type' => 'string'],
        'age' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 150]
    ],
    'required' => ['name', 'age']
];

if (!$json->validateSchema($schema)) {
    throw new ValidationException('Data does not match expected schema');
}
```

**Why**: Ensures data conforms to expected structure and prevents:
- Type confusion vulnerabilities
- Injection attacks (via malformed data)
- SQL injection (when data is used in queries)
- XSS (when data is rendered in HTML)

### 3. Type Coercion Safety

Be cautious when using lenient type coercion with untrusted data:

```php
// Use strict mode for security-sensitive operations
$json->enableStrictCoercion(true);

try {
    $userId = $json->coerceInt($untrustedValue);
} catch (InvalidArgumentException $e) {
    // Handle type mismatch instead of silent conversion
}
```

**Why**: Lenient coercion can lead to unexpected type conversions:
- `"0e0" == 0` (scientific notation)
- `"1abc" == 1` (leading digits)
- `null` coerces to `0` (unexpected behavior)

### 4. Event Hooks for Custom Validation

Use event dispatchers to inject custom validation logic at key points:

```php
$dispatcher = $json->getDispatcher();

$dispatcher->subscribe('before_merge', function($event) {
    // Validate merge operation before it occurs
    $sourceData = $event->getPayload()['operand2'] ?? null;
    
    if (/* detected malicious pattern */) {
        throw new SecurityException('Malicious data detected');
    }
});
```

**Why**: Provides checkpoints to:
- Audit data transformations
- Enforce business rules
- Block suspicious patterns
- Log security events

### 5. Path Traversal Prevention

When using paths, validate path strings to prevent traversal attacks:

```php
// Use whitelisted paths
$allowedPaths = ['name', 'email', 'phone'];
$path = $_GET['field'] ?? null;

if (!in_array($path, $allowedPaths)) {
    throw new SecurityException('Invalid field');
}

$value = $json->get($path);
```

**Why**: Prevents:
- Information disclosure
- Unintended data access
- Prototype pollution

### 6. Exception Hygiene

Never expose sensitive information in error messages or exceptions:

```php
try {
    $json = Json::parse($input);
} catch (ParseException $e) {
    // Log full error for debugging (internal only)
    error_log($e->getMessage());
    
    // Return generic error to client
    throw new ValidationException('Invalid JSON format');
}
```

**Why**: Prevents information leakage to attackers

### 7. Safe Defaults

The library provides safe defaults for common operations:

- **Max depth**: Configurable to prevent stack overflow
- **Sanitization**: Optional sanitization for sensitive data
- **Validation**: Type-strict by default in core operations
- **Error handling**: Exceptions instead of silent failures

### 8. Regular Updates

Keep the library updated to the latest version:

```bash
composer update skpassegna/json-parser
```

**Why**: Security patches and vulnerability fixes are released regularly

### 9. Procedural API Security

When using procedural functions, apply the same security practices:

```php
use function Skpassegna\Json\Procedural\{
    json_parse,
    json_validate,
    json_get
};

// Validate before parsing
$json = json_parse($input, ['max_depth' => 10]);

// Validate against schema
if (!json_validate($json, $schema)) {
    throw new ValidationException('Invalid data');
}

// Use safe defaults for path access
$value = json_get($json, $allowedPath, 'default');
```

### 10. Compliance and Standards

This library follows security best practices from:

- **OWASP** - JSON Security guidelines
- **RFC 7159** - JSON specification
- **RFC 6902** - JSON Patch (validated operations)
- **RFC 7396** - JSON Merge Patch (safe merging)
- **PHP Security Guidelines** - Type safety and error handling

## Security Resources

- [OWASP JSON Security Guidelines](https://owasp.org/www-community/attacks/JSON_Injection)
- [PHP Secure Coding Guidelines](https://phptherightway.com/#security)

## Security Updates

Security updates will be released as soon as possible after a vulnerability is discovered and verified. Updates will be published through:

1. GitHub Security Advisories
2. Release Notes
3. Security Notifications to registered users

## Acknowledgments

We would like to thank the following individuals and organizations who have helped improve the security of this library:

- List will be updated as contributors help identify and fix security issues
