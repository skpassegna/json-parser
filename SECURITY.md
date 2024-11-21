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

1. Always validate and sanitize JSON input before processing
2. Use appropriate depth and length limits when parsing JSON
3. Implement proper error handling
4. Keep the library updated to the latest version
5. Follow secure coding practices in your implementation

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
