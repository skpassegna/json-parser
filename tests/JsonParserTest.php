<?php

namespace Skpassegna\JsonParser\Tests;

use Skpassegna\JsonParser\JsonValue;
use Skpassegna\JsonParser\JsonParser;
use Skpassegna\JsonParser\JsonObject;
use Skpassegna\JsonParser\JsonArray;
use Skpassegna\JsonParser\Exceptions\HumanReadableJsonException;
use PHPUnit\Framework\TestCase;

class JsonParserTest extends TestCase
{
    /**
     * @covers \Skpassegna\JsonParser\JsonParser::parse
     *
     * Test that the parse method correctly parses a valid JSON string into a JsonObject instance.
     *
     * @dataProvider validJsonObjectProvider
     * @param string $jsonString
     * @param array $expectedData
     */
    public function testParseValidJsonObject(string $jsonString, array $expectedData)
    {
        $parser = new JsonParser();
        $jsonObject = $parser->parse($jsonString);

        $this->assertInstanceOf(JsonObject::class, $jsonObject);
        $this->assertEquals($expectedData, $jsonObject->toArray());
    }

    /**
     * Data provider for valid JSON object strings.
     *
     * @return array
     */
    public function validJsonObjectProvider(): array
    {
        return [
            'simple object' => ['{"name": "John", "age": 30}', ['name' => 'John', 'age' => 30]],
            'nested object' => ['{"person": {"name": "Alice", "age": 25}, "city": "New York"}', ['person' => ['name' => 'Alice', 'age' => 25], 'city' => 'New York']],
            'empty object' => ['{}', []],
        ];
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonParser::parse
     *
     * Test that the parse method correctly parses a valid JSON string into a JsonArray instance.
     *
     * @dataProvider validJsonArrayProvider
     * @param string $jsonString
     * @param array $expectedData
     */
    public function testParseValidJsonArray(string $jsonString, array $expectedData)
    {
        $parser = new JsonParser();
        $jsonArray = $parser->parse($jsonString);

        $this->assertInstanceOf(JsonArray::class, $jsonArray);
        $this->assertEquals($expectedData, $jsonArray->toArray());
    }

    /**
     * Data provider for valid JSON array strings.
     *
     * @return array
     */
    public function validJsonArrayProvider(): array
    {
        return [
            'simple array' => ['[1, 2, 3]', [1, 2, 3]],
            'nested array' => ['[[1, 2], [3, 4], [5, 6]]', [[1, 2], [3, 4], [5, 6]]],
            'empty array' => ['[]', []],
        ];
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonParser::parse
     *
     * Test that the parse method correctly parses a valid JSON string into a JsonValue instance
     * for scalar values (boolean, number, string, null).
     *
     * @dataProvider validJsonValueProvider
     * @param string $jsonString
     * @param mixed $expectedValue
     */
    public function testParseValidJsonValue(string $jsonString, $expectedValue)
    {
        $parser = new JsonParser();
        $jsonValue = $parser->parse($jsonString);

        $this->assertInstanceOf(JsonValue::class, $jsonValue);
        $this->assertEquals($expectedValue, $jsonValue->getValue());
    }

    /**
     * Data provider for valid JSON scalar values.
     *
     * @return array
     */
    public function validJsonValueProvider(): array
    {
        return [
            'boolean true' => ['true', true],
            'boolean false' => ['false', false],
            'integer' => ['42', 42],
            'float' => ['3.14', 3.14],
            'string' => ['"hello"', 'hello'],
            'null' => ['null', null],
        ];
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonParser::parse
     *
     * Test that the parse method throws a HumanReadableJsonException when trying to parse an invalid JSON string.
     *
     * @dataProvider invalidJsonProvider
     * @param string $invalidJsonString
     * @param string $expectedErrorMessage
     */
    public function testParseInvalidJson(string $invalidJsonString, string $expectedErrorMessage)
    {
        $this->expectException(HumanReadableJsonException::class);
        $this->expectExceptionMessage($expectedErrorMessage);

        $parser = new JsonParser();
        $parser->parse($invalidJsonString);
    }

    /**
     * Data provider for invalid JSON strings and expected error messages.
     *
     * @return array
     */
    public function invalidJsonProvider(): array
    {
        return [
            'trailing comma' => ['{"name": "John", "age": 30,}', 'Invalid or malformed JSON data. The syntax is incorrect.'],
            'missing quote' => ['{"name": John, "age": 30}', 'Invalid or malformed JSON data. The syntax is incorrect.'],
            'invalid control character' => ['{"name": "John", "age": ' . chr(0x1F) . '30}', 'Invalid or malformed JSON data. The parser encountered an unexpected control character.'],
            'invalid UTF-8' => ['{"name": "John", "age": ' . chr(0xC0) . '}', 'Invalid or malformed JSON data. The input is not valid UTF-8 encoded.'],
        ];
    }
}