# JSON Parser Pull Request

## Description
Please include a summary of the change and which issue is fixed. Include relevant motivation, context, and JSON-specific considerations.

Fixes # (issue)

## Type of change
Please delete options that are not relevant.

- [ ] Bug fix (non-breaking change which fixes an issue)
- [ ] New feature (non-breaking change which adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to not work as expected)
- [ ] Performance improvement (JSON parsing/encoding optimization)
- [ ] Security enhancement (JSON vulnerability fix)
- [ ] Documentation update
- [ ] Code style update (formatting, renaming)
- [ ] Test update

## JSON-specific Considerations
- [ ] Changes maintain RFC 8259 compliance
- [ ] Proper handling of Unicode characters
- [ ] Proper error handling for malformed JSON
- [ ] Memory usage optimization for large JSON files
- [ ] Proper handling of numeric precision
- [ ] Security considerations for untrusted JSON input

## Testing Details
Please describe the tests you've added or modified:

- [ ] Unit Tests
  - [ ] Parser tests
  - [ ] Validator tests
  - [ ] Schema tests
  - [ ] Error handling tests
- [ ] Integration Tests
- [ ] Performance Tests
  - [ ] Large JSON files
  - [ ] Complex nested structures
  - [ ] Memory usage tests
- [ ] Security Tests
  - [ ] Injection prevention
  - [ ] DoS protection
  - [ ] UTF-8 handling

## Test Configuration
* PHP version:
* Operating System:
* Memory limit:
* JSON file sizes tested:
* Unicode test cases included:

## Performance Impact
For performance-related changes:
- [ ] Benchmarks run before and after changes
- [ ] Memory profiling completed
- [ ] Large JSON file testing completed
- [ ] No significant performance regression

## Security Checklist
- [ ] Input validation implemented
- [ ] Proper sanitization for JSON input
- [ ] Memory limit considerations
- [ ] No sensitive data exposure
- [ ] Proper error message handling (no internal details exposed)

## General Checklist
- [ ] Code follows project style guidelines
- [ ] Self-review completed
- [ ] Documentation updated
- [ ] CHANGELOG.md updated
- [ ] Version numbers updated (if applicable)
- [ ] All tests pass locally
- [ ] Benchmarks show no significant regression
- [ ] Security checks passed

## Additional Notes
Add any additional notes about the PR here, especially regarding JSON parsing edge cases or compatibility considerations.
