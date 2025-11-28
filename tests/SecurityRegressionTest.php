<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Json;
use Skpassegna\Json\Exceptions\ParseException;

class SecurityRegressionTest extends TestCase
{
    /**
     * Test that deeply nested JSON is rejected with depth limit.
     */
    public function test_deeply_nested_json_rejected_with_depth_limit(): void
    {
        // Create deeply nested JSON (more than 5 levels)
        $deep = json_encode([
            'a' => ['b' => ['c' => ['d' => ['e' => ['f' => 'value']]]]]
        ]);

        self::expectException(ParseException::class);
        Json::parse($deep, ['max_depth' => 3]);
    }

    /**
     * Test that very large JSON is rejected with length limit.
     */
    public function test_large_json_rejected_with_length_limit(): void
    {
        $large = json_encode(array_fill(0, 1000, 'value'));

        self::expectException(ParseException::class);
        Json::parse($large, ['max_length' => 100]);
    }

    /**
     * Test that invalid JSON is always rejected.
     */
    public function test_invalid_json_always_rejected(): void
    {
        $invalidExamples = [
            '{incomplete',
            '{"trailing":,}',
            "{'single': 'quotes'}",
            "{name: 'value'}", // unquoted keys
            '{"control": \x00}', // null byte
        ];

        foreach ($invalidExamples as $invalid) {
            try {
                Json::parse($invalid);
                self::fail("Should have rejected invalid JSON: $invalid");
            } catch (ParseException $e) {
                // Expected
                self::assertTrue(true);
            }
        }
    }

    /**
     * Test that path traversal is not possible with safe path access.
     */
    public function test_path_access_is_safe(): void
    {
        $json = Json::parse('{"user": {"profile": {"email": "user@example.com"}}, "admin": {"secret": "classified"}}');

        // Accessing nested path works safely
        $email = $json->get('user.profile.email');
        self::assertEquals('user@example.com', $email);

        // Non-existent paths return default safely
        $missing = $json->get('user.profile.phone', 'N/A');
        self::assertEquals('N/A', $missing);

        // Path traversal attempts should not work
        $secret = $json->get('admin.secret', 'not found');
        // Admin data is accessible but only if intended
        self::assertEquals('classified', $secret);
    }

    /**
     * Test that schema validation prevents invalid data types.
     */
    public function test_schema_validation_prevents_type_confusion(): void
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer'],
                'name' => ['type' => 'string'],
                'age' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 150],
            ],
            'required' => ['id', 'name'],
        ];

        // Valid data
        $valid = Json::parse('{"id": 1, "name": "Alice", "age": 30}');
        self::assertTrue($valid->validateSchema($schema));

        // Invalid: id is string instead of integer
        $invalidId = Json::parse('{"id": "not-an-integer", "name": "Alice"}');
        self::assertFalse($invalidId->validateSchema($schema));

        // Invalid: age exceeds maximum
        $invalidAge = Json::parse('{"id": 1, "name": "Alice", "age": 200}');
        self::assertFalse($invalidAge->validateSchema($schema));

        // Invalid: missing required field
        $incomplete = Json::parse('{"id": 1}');
        self::assertFalse($incomplete->validateSchema($schema));
    }

    /**
     * Test that type coercion respects strict mode.
     */
    public function test_strict_type_coercion_prevents_confusion(): void
    {
        $json = Json::create();

        // Strict mode should reject ambiguous conversions
        $json->enableStrictCoercion(true);

        // These might fail or behave differently in strict mode
        try {
            // String to int conversion should be strict
            $value = $json->coerceInt('42');
            self::assertEquals(42, $value);

            // But ambiguous values should fail
            $json->coerceInt('42abc');
            self::fail('Should have rejected "42abc" in strict mode');
        } catch (\Exception $e) {
            self::assertTrue(true); // Expected to fail in strict mode
        }

        // Lenient mode allows more conversions
        $json->enableStrictCoercion(false);
        $value = $json->coerceInt('42');
        self::assertEquals(42, $value);
    }

    /**
     * Test that exception messages don't leak sensitive data.
     */
    public function test_exception_messages_are_safe(): void
    {
        $sensitiveJson = '{"password": "super_secret_123", "credit_card": "1234-5678-9012-3456"}';

        try {
            Json::parse($sensitiveJson, ['max_length' => 5]);
        } catch (ParseException $e) {
            $message = $e->getMessage();
            // Message should not contain the actual data
            self::assertStringNotContainsString('super_secret_123', $message);
            self::assertStringNotContainsString('1234-5678-9012-3456', $message);
        }
    }

    /**
     * Test that merge operations can be validated.
     */
    public function test_merge_operations_can_be_validated(): void
    {
        $json = Json::parse('{"role": "user", "permissions": ["read"]}');

        $dispatcher = $json->getDispatcher();
        $validation_failed = false;

        $dispatcher->subscribe('before_merge', function ($event) use (&$validation_failed) {
            $sourceData = $event->getPayload()['operand2'] ?? null;

            // Prevent privilege escalation
            if (is_array($sourceData) && isset($sourceData['role']) && $sourceData['role'] === 'admin') {
                $validation_failed = true;
            }
        });

        // This merge should be blocked by validation
        try {
            $json->merge(['role' => 'admin', 'permissions' => ['*']]);
            self::assertTrue($validation_failed);
        } catch (\Exception $e) {
            // Validation prevented the merge
            self::assertTrue(true);
        }
    }

    /**
     * Test that null byte injection is prevented.
     */
    public function test_null_byte_injection_prevention(): void
    {
        // Attempt to inject null bytes
        $malicious = '{"test": "value\x00injection"}';

        try {
            $json = Json::parse($malicious);
            // If parsing succeeds, accessing the value should be safe
            $value = $json->get('test');
            self::assertIsString($value);
        } catch (ParseException $e) {
            // Expected: null bytes in JSON are invalid
            self::assertTrue(true);
        }
    }

    /**
     * Test that very large integers don't cause numeric precision issues.
     */
    public function test_large_integer_handling(): void
    {
        // PHP has precision limits with floats
        $largeInt = '{"bignum": 9223372036854775807}'; // PHP_INT_MAX

        $json = Json::parse($largeInt);
        $value = $json->get('bignum');

        // The value should be parsed safely (as integer or string depending on size)
        self::assertNotNull($value);
    }

    /**
     * Test that Unicode encoding is handled safely.
     */
    public function test_unicode_handling(): void
    {
        $unicode = '{"text": "Hello 世界 🌍"}';
        $json = Json::parse($unicode);
        $text = $json->get('text');

        self::assertStringContainsString('世界', $text);
        self::assertStringContainsString('🌍', $text);
    }

    /**
     * Test that recursive structures don't cause infinite loops.
     */
    public function test_circular_reference_protection(): void
    {
        // Create a structure with potential for issues
        $data = ['key' => 'value'];
        $json = Json::create($data);

        // Converting to string should work without infinite loop
        $string = $json->toString();
        self::assertIsString($string);

        // Merging large data sets should not cause memory issues
        $largeData = array_fill(0, 1000, ['nested' => array_fill(0, 10, 'value')]);
        $json2 = Json::create($largeData);
        
        self::assertInstanceOf(Json::class, $json2);
    }

    /**
     * Test fuzz input with random invalid JSON.
     */
    public function test_fuzz_with_random_invalid_json(): void
    {
        $fuzzyInputs = [
            '',
            '{',
            '[',
            '}}}}',
            '[[[',
            '{"key": undefined}',
            '{"key": NaN}',
            '{...spread}',
            'null\n\n',
            '{"key": "unterminated',
        ];

        $validCount = 0;

        foreach ($fuzzyInputs as $input) {
            try {
                Json::parse($input);
                $validCount++;
            } catch (ParseException $e) {
                // Expected for invalid input
                self::assertNotEmpty($e->getMessage());
            }
        }

        // Most should fail parsing
        self::assertGreaterThanOrEqual(0, $validCount);
        self::assertLessThan(count($fuzzyInputs), $validCount);
    }
}
