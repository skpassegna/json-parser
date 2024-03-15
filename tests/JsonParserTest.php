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
    public function testParseObject()
    {
        $jsonString = '{"name":"John Doe","age":30,"address":{"street":"123 Main St","city":"Anytown","state":"CA"}}';
        $parser = new JsonParser();
        $jsonObject = $parser->parse($jsonString);

        $this->assertInstanceOf(JsonObject::class, $jsonObject);
        $this->assertEquals('John Doe', $jsonObject->get('name'));
        $this->assertEquals(30, $jsonObject->get('age'));
        $this->assertEquals('123 Main St', $jsonObject->get('address.street'));
    }

    public function testParseArray()
    {
        $jsonString = '[1, 2, 3, 4, 5]';
        $parser = new JsonParser();
        $jsonArray = $parser->parse($jsonString);

        $this->assertInstanceOf(JsonArray::class, $jsonArray);
        $this->assertEquals([1, 2, 3, 4, 5], $jsonArray->toArray());
    }

    public function testParseValue()
    {
        $jsonString = '"test"';
        $parser = new JsonParser();
        $jsonValue = $parser->parse($jsonString);

        $this->assertInstanceOf(JsonValue::class, $jsonValue);
        $this->assertEquals('test', $jsonValue->getValue());

        $jsonString = 'true';
        $parser = new JsonParser();
        $jsonValue = $parser->parse($jsonString);

        $this->assertInstanceOf(JsonValue::class, $jsonValue);
        $this->assertTrue($jsonValue->getValue());

        $jsonString = 'null';
        $parser = new JsonParser();
        $jsonValue = $parser->parse($jsonString);

        $this->assertInstanceOf(JsonValue::class, $jsonValue);
        $this->assertNull($jsonValue->getValue());
    }

    public function testParseInvalidJson()
    {
        $this->expectException(HumanReadableJsonException::class);
        $jsonString = '{invalid}';
        $parser = new JsonParser();
        $parser->parse($jsonString);
    }
}