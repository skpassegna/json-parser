<?php

namespace Skpassegna\JsonParser\Tests;

use Skpassegna\JsonParser\JsonValue;
use PHPUnit\Framework\TestCase;

class JsonValueTest extends TestCase
{
    public function testGetValue()
    {
        $value = 'test';
        $jsonValue = new JsonValue($value);
        $this->assertEquals($value, $jsonValue->getValue());
    }

    public function testToString()
    {
        $value = 'test';
        $jsonValue = new JsonValue($value);
        $this->assertEquals($value, (string)$jsonValue);
    }

    public function testIsBoolean()
    {
        $jsonValue = new JsonValue(true);
        $this->assertTrue($jsonValue->isBoolean());

        $jsonValue = new JsonValue(false);
        $this->assertTrue($jsonValue->isBoolean());

        $jsonValue = new JsonValue('test');
        $this->assertFalse($jsonValue->isBoolean());
    }

    public function testIsNumber()
    {
        $jsonValue = new JsonValue(42);
        $this->assertTrue($jsonValue->isNumber());

        $jsonValue = new JsonValue(3.14);
        $this->assertTrue($jsonValue->isNumber());

        $jsonValue = new JsonValue('test');
        $this->assertFalse($jsonValue->isNumber());
    }

    public function testIsInteger()
    {
        $jsonValue = new JsonValue(42);
        $this->assertTrue($jsonValue->isInteger());

        $jsonValue = new JsonValue(3.14);
        $this->assertFalse($jsonValue->isInteger());

        $jsonValue = new JsonValue('test');
        $this->assertFalse($jsonValue->isInteger());
    }

    public function testIsFloat()
    {
        $jsonValue = new JsonValue(42);
        $this->assertFalse($jsonValue->isFloat());

        $jsonValue = new JsonValue(3.14);
        $this->assertTrue($jsonValue->isFloat());

        $jsonValue = new JsonValue('test');
        $this->assertFalse($jsonValue->isFloat());
    }

    public function testIsString()
    {
        $jsonValue = new JsonValue('test');
        $this->assertTrue($jsonValue->isString());

        $jsonValue = new JsonValue(42);
        $this->assertFalse($jsonValue->isString());
    }

    public function testIsNull()
    {
        $jsonValue = new JsonValue(null);
        $this->assertTrue($jsonValue->isNull());

        $jsonValue = new JsonValue('test');
        $this->assertFalse($jsonValue->isNull());
    }
}