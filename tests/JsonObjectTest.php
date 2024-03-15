<?php

namespace Skpassegna\JsonParser\Tests;

use Skpassegna\JsonParser\JsonObject;
use Skpassegna\JsonParser\Exceptions\JsonKeyNotFoundException;
use PHPUnit\Framework\TestCase;

class JsonObjectTest extends TestCase
{
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

        $this->assertEquals('John Doe', $jsonObject->get('name'));
        $this->assertEquals(30, $jsonObject->get('age'));
        $this->assertEquals('123 Main St', $jsonObject->get('address.street'));
        $this->assertEquals('CA', $jsonObject->get('address.state'));
        $this->assertNull($jsonObject->get('nonexistent', null));

        $this->expectException(JsonKeyNotFoundException::class);
        $jsonObject->get('nonexistent');
    }

    public function testSet()
    {
        $jsonObject = new JsonObject();

        $jsonObject->set('name', 'John Doe');
        $this->assertEquals('John Doe', $jsonObject->get('name'));

        $jsonObject->set('address.street', '123 Main St');
        $this->assertEquals('123 Main St', $jsonObject->get('address.street'));
    }

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

        $this->assertTrue($jsonObject->has('name'));
        $this->assertTrue($jsonObject->has('age'));
        $this->assertTrue($jsonObject->has('address.street'));
        $this->assertFalse($jsonObject->has('nonexistent'));
    }

    public function testRemove()
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

        $jsonObject->remove('age');
        $this->assertFalse($jsonObject->has('age'));

        $jsonObject->remove('address.state');
        $this->assertFalse($jsonObject->has('address.state'));

        $this->expectException(JsonKeyNotFoundException::class);
        $jsonObject->remove('nonexistent');
    }

    public function testToArray()
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

        $this->assertEquals($data, $jsonObject->toArray());
    }

    public function testToJson()
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

        $this->assertEquals(json_encode($data), $jsonObject->toJson());
    }

    public function testIteration()
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

        $expected = [
            'name' => 'John Doe',
            'age' => 30,
            'address' => [
                'street' => '123 Main St',
                'city' => 'Anytown',
                'state' => 'CA',
            ],
        ];

        $result = [];
        foreach ($jsonObject as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals($expected, $result);
    }
}