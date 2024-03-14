<?php

namespace Skpassegna\JsonParser\Tests;

use Skpassegna\JsonParser\JsonValue;
use PHPUnit\Framework\TestCase;

class JsonValueTest extends TestCase
{
    /**
     * @covers \Skpassegna\JsonParser\JsonValue::__construct
     * @covers \Skpassegna\JsonParser\JsonValue::getValue
     *
     * Test that the JsonValue constructor sets the value correctly and getValue returns the expected value.
     *
     * @dataProvider constructorDataProvider
     * @param mixed $value
     */
    public function testConstructor($value)
    {
        $jsonValue = new JsonValue($value);
        $this->assertEquals($value, $jsonValue->getValue());
    }

    /**
     * Data provider for the JsonValue constructor.
     *
     * @return array
     */
    public function constructorDataProvider(): array
    {
        return [
            'boolean true' => [true],
            'boolean false' => [false],
            'integer' => [42],
            'float' => [3.14],
            'string' => ['hello'],
            'null' => [null],
        ];
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonValue::isBoolean
     *
     * Test the isBoolean method for checking if the value is a boolean.
     *
     * @dataProvider booleanDataProvider
     * @param mixed $value
     * @param bool $expected
     */
    public function testIsBoolean($value, bool $expected)
    {
        $jsonValue = new JsonValue($value);
        $this->assertEquals($expected, $jsonValue->isBoolean());
    }

    /**
     * Data provider for the isBoolean method.
     *
     * @return array
     */
    public function booleanDataProvider(): array
    {
        return [
            'boolean true' => [true, true],
            'boolean false' => [false, true],
            'integer' => [42, false],
            'float' => [3.14, false],
            'string' => ['hello', false],
            'null' => [null, false],
        ];
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonValue::isNumber
     *
     * Test the isNumber method for checking if the value is a number.
     *
     * @dataProvider numberDataProvider
     * @param mixed $value
     * @param bool $expected
     */
    public function testIsNumber($value, bool $expected)
    {
        $jsonValue = new JsonValue($value);
        $this->assertEquals($expected, $jsonValue->isNumber());
    }

    /**
     * Data provider for the isNumber method.
     *
     * @return array
     */
    public function numberDataProvider(): array
    {
        return [
            'boolean true' => [true, false],
            'boolean false' => [false, false],
            'integer' => [42, true],
            'float' => [3.14, true],
            'string' => ['hello', false],
            'null' => [null, false],
        ];
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonValue::isInteger
     *
     * Test the isInteger method for checking if the value is an integer.
     *
     * @dataProvider integerDataProvider
     * @param mixed $value
     * @param bool $expected
     */
    public function testIsInteger($value, bool $expected)
    {
        $jsonValue = new JsonValue($value);
        $this->assertEquals($expected, $jsonValue->isInteger());
    }

    /**
     * Data provider for the isInteger method.
     *
     * @return array
     */
    public function integerDataProvider(): array
    {
        return [
            'boolean true' => [true, false],
            'boolean false' => [false, false],
            'integer' => [42, true],
            'float' => [3.14, false],
            'string' => ['hello', false],
            'null' => [null, false],
        ];
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonValue::isFloat
     *
     * Test the isFloat method for checking if the value is a float.
     *
     * @dataProvider floatDataProvider
     * @param mixed $value
     * @param bool $expected
     */
    public function testIsFloat($value, bool $expected)
    {
        $jsonValue = new JsonValue($value);
        $this->assertEquals($expected, $jsonValue->isFloat());
    }

    /**
     * Data provider for the isFloat method.
     *
     * @return array
     */
    public function floatDataProvider(): array
    {
        return [
            'boolean true' => [true, false],
            'boolean false' => [false, false],
            'integer' => [42, false],
            'float' => [3.14, true],
            'string' => ['hello', false],
            'null' => [null, false],
        ];
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonValue::isString
     *
     * Test the isString method for checking if the value is a string.
     *
     * @dataProvider stringDataProvider
     * @param mixed $value
     * @param bool $expected
     */
    public function testIsString($value, bool $expected)
    {
        $jsonValue = new JsonValue($value);
        $this->assertEquals($expected, $jsonValue->isString());
    }

    /**
     * Data provider for the isString method.
     *
     * @return array
     */
    public function stringDataProvider(): array
    {
        return [
            'boolean true' => [true, false],
            'boolean false' => [false, false],
            'integer' => [42, false],
            'float' => [3.14, false],
            'string' => ['hello', true],
            'null' => [null, false],
        ];
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonValue::isNull
     *
     * Test the isNull method for checking if the value is null.
     *
     * @dataProvider nullDataProvider
     * @param mixed $value
     * @param bool $expected
     */
    public function testIsNull($value, bool $expected)
    {
        $jsonValue = new JsonValue($value);
        $this->assertEquals($expected, $jsonValue->isNull());
    }

    /**
     * Data provider for the isNull method.
     *
     * @return array
     */
    public function nullDataProvider(): array
    {
        return [
            'boolean true' => [true, false],
            'boolean false' => [false, false],
            'integer' => [42, false],
            'float' => [3.14, false],
            'string' => ['hello', false],
            'null' => [null, true],
        ];
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonValue::__toString
     *
     * Test that the __toString method returns the correct string representation of the value.
     *
     * @dataProvider toStringDataProvider
     * @param mixed $value
     * @param string $expected
     */
    public function testToString($value, string $expected)
    {
        $jsonValue = new JsonValue($value);
        $this->assertEquals($expected, (string)$jsonValue);
    }

    /**
     * Data provider for the __toString method.
     *
     * @return array
     */
    public function toStringDataProvider(): array
    {
        return [
            'boolean true' => [true, 'true'],
            'boolean false' => [false, 'false'],
            'integer' => [42, '42'],
            'float' => [3.14, '3.14'],
            'string' => ['hello', 'hello'],
            'null' => [null, ''],
        ];
    }
}