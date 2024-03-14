<?php

namespace Skpassegna\JsonParser\Tests;

use Skpassegna\JsonParser\JsonObject;
use Skpassegna\JsonParser\JsonHelper;
use Skpassegna\JsonParser\JsonArray;
use PHPUnit\Framework\TestCase;

class JsonHelperTest extends TestCase
{
    /**
     * @covers \Skpassegna\JsonParser\JsonHelper::has
     *
     * Test the has method for checking if a value exists in a JsonObject or JsonArray by key path.
     *
     * @dataProvider hasDataProvider
     * @param mixed $data
     * @param string $keyPath
     * @param bool $expected
     */
    public function testHas($data, string $keyPath, bool $expected)
    {
        $this->assertEquals($expected, JsonHelper::has($data, $keyPath));
    }

    /**
     * Data provider for the has method.
     *
     * @return array
     */
    public function hasDataProvider(): array
    {
        $jsonObject = new JsonObject([
            'name' => 'John',
            'age' => 30,
            'addresses' => [
                'home' => '123 Main St.',
                'work' => '456 Office Rd.'
            ]
        ]);

        $jsonArray = new JsonArray([1, 2, 3, 4, 5]);

        return [
            'object simple property' => [$jsonObject, 'name', true],
            'object nested property' => [$jsonObject, 'addresses.home', true],
            'object non-existent property' => [$jsonObject, 'non.existent.key', false],
            'array index' => [$jsonArray, '2', true],
            'array non-existent index' => [$jsonArray, '10', false],
        ];
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonHelper::get
     *
     * Test the get method for getting a value from a JsonObject or JsonArray by key path.
     *
     * @dataProvider getDataProvider
     * @param mixed $data
     * @param string $keyPath
     * @param mixed $expected
     */
    public function testGet($data, string $keyPath, $expected)
    {
        $this->assertEquals($expected, JsonHelper::get($data, $keyPath));
    }

    /**
     * Data provider for the get method.
     *
     * @return array
     */
    public function getDataProvider(): array
    {
        $jsonObject = new JsonObject([
            'name' => 'John',
            'age' => 30,
            'addresses' => [
                'home' => '123 Main St.',
                'work' => '456 Office Rd.'
            ]
        ]);

        $jsonArray = new JsonArray([1, 2, 3, 4, 5]);

        return [
            'object simple property' => [$jsonObject, 'name', 'John'],
            'object nested property' => [$jsonObject, 'addresses.home', '123 Main St.'],
            'object non-existent property' => [$jsonObject, 'non.existent.key', null],
            'array index' => [$jsonArray, '2', 3],
            'array non-existent index' => [$jsonArray, '10', null],
        ];
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonHelper::get
     *
     * Test the get method with a custom default value when the key path doesn't exist.
     *
     * @dataProvider getWithDefaultDataProvider
     * @param mixed $data
     * @param string $keyPath
     * @param mixed $default
     * @param mixed $expected
     */
    public function testGetWithDefault($data, string $keyPath, $default, $expected)
    {
        $this->assertEquals($expected, JsonHelper::get($data, $keyPath, $default));
    }

    /**
     * Data provider for the get method with a custom default value.
     *
     * @return array
     */
    public function getWithDefaultDataProvider(): array
    {
        $jsonObject = new JsonObject([
            'name' => 'John',
            'age' => 30,
            'addresses' => [
                'home' => '123 Main St.',
                'work' => '456 Office Rd.'
            ]
        ]);

        $jsonArray = new JsonArray([1, 2, 3, 4, 5]);

        return [
            'object non-existent property with default' => [$jsonObject, 'non.existent.key', 'default value', 'default value'],
            'array non-existent index with default' => [$jsonArray, '10', 'default value', 'default value'],
        ];
    }
}