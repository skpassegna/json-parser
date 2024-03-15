<?php

namespace Skpassegna\JsonParser\Tests;

use Skpassegna\JsonParser\JsonObject;
use Skpassegna\JsonParser\JsonHelper;
use Skpassegna\JsonParser\JsonArray;
use Skpassegna\JsonParser\Exceptions\JsonKeyNotFoundException;
use PHPUnit\Framework\TestCase;

class JsonHelperTest extends TestCase
{
    public function testHas()
    {
        $data = [
            'name' => 'John Doe',
            'age' => 30,
            'address' => [
                'street' => '123 Main St',
                'city' => 'Anytown',
                'state' => 'CA',
            ],
        ];

        $jsonObject = new JsonObject($data);
        $jsonArray = new JsonArray($data);

        $this->assertTrue(JsonHelper::has($jsonObject, 'name'));
        $this->assertTrue(JsonHelper::has($jsonObject, 'address.street'));
        $this->assertFalse(JsonHelper::has($jsonObject, 'nonexistent'));

        $this->assertTrue(JsonHelper::has($jsonArray, '0'));
        $this->assertTrue(JsonHelper::has($jsonArray, '2.street'));
        $this->assertFalse(JsonHelper::has($jsonArray, 'nonexistent'));

        $this->expectException(JsonKeyNotFoundException::class);
        JsonHelper::has($jsonArray, 'nonexistent');
    }

    public function testGet()
    {
        $data = [
            'name' => 'John Doe',
            'age' => 30,
            'address' => [
                'street' => '123 Main St',
                'city' => 'Anytown',
                'state' => 'CA',
            ],
        ];

        $jsonObject = new JsonObject($data);
        $jsonArray = new JsonArray($data);

        $this->assertEquals('John Doe', JsonHelper::get($jsonObject, 'name'));
        $this->assertEquals('123 Main St', JsonHelper::get($jsonObject, 'address.street'));
        $this->assertNull(JsonHelper::get($jsonObject, 'nonexistent', null));

        $this->assertEquals($data, JsonHelper::get($jsonArray, '0', []));
        $this->assertEquals('123 Main St', JsonHelper::get($jsonArray, '2.street'));
        $this->assertNull(JsonHelper::get($jsonArray, 'nonexistent', null));

        $this->expectException(JsonKeyNotFoundException::class);
        JsonHelper::get($jsonArray, 'nonexistent');
    }
}