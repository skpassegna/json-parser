<?php

namespace Skpassegna\JsonParser\Tests;

use Skpassegna\JsonParser\JsonObject;
use PHPUnit\Framework\TestCase;

class JsonObjectTest extends TestCase
{
    /**
     * @covers \Skpassegna\JsonParser\JsonObject::__construct
     * @covers \Skpassegna\JsonParser\JsonObject::toArray
     *
     * Test that the JsonObject constructor sets the data correctly and toArray returns the expected array.
     *
     * @dataProvider constructorDataProvider
     * @param array $data
     */
    public function testConstructor(array $data)
    {
        $jsonObject = new JsonObject($data);
        $this->assertEquals($data, $jsonObject->toArray());
    }

    /**
     * Data provider for the JsonObject constructor.
     *
     * @return array
     */
    public function constructorDataProvider(): array
    {
        return [
            'empty object' => [[]],
            'simple object' => [['name' => 'John', 'age' => 30]],
            'nested object' => [['person' => ['name' => 'Alice', 'age' => 25], 'city' => 'New York']],
        ];
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonObject::get
     * @covers \Skpassegna\JsonParser\JsonObject::set
     * @covers \Skpassegna\JsonParser\JsonObject::has
     * @covers \Skpassegna\JsonParser\JsonObject::remove
     *
     * Test the get, set, has, and remove methods for properties in a JsonObject.
     */
    public function testPropertyOperations()
    {
        $jsonObject = new JsonObject([
            'name' => 'John',
            'age' => 30,
            'addresses' => [
                'home' => '123 Main St.',
                'work' => '456 Office Rd.'
            ]
        ]);

        // Test getting properties
        $this->assertTrue($jsonObject->has('name'));
        $this->assertEquals('John', $jsonObject->get('name'));
        $this->assertEquals('123 Main St.', $jsonObject->get('addresses.home'));

        // Test setting properties
        $jsonObject->set('email', 'john@example.com');
        $this->assertTrue($jsonObject->has('email'));
        $this->assertEquals('john@example.com', $jsonObject->get('email'));

        $jsonObject->set('addresses.home', '456 New St.');
        $this->assertEquals('456 New St.', $jsonObject->get('addresses.home'));

        // Test removing properties
        $jsonObject->remove('age');
        $this->assertFalse($jsonObject->has('age'));

        $jsonObject->remove('addresses.work');
        $this->assertFalse($jsonObject->has('addresses.work'));
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonObject::__get
     * @covers \Skpassegna\JsonParser\JsonObject::__set
     * @covers \Skpassegna\JsonParser\JsonObject::__isset
     * @covers \Skpassegna\JsonParser\JsonObject::__unset
     *
     * Test the magic methods for property access in a JsonObject.
     */
    public function testMagicMethods()
    {
        $jsonObject = new JsonObject([
            'name' => 'John',
            'age' => 30,
            'addresses' => [
                'home' => '123 Main St.',
                'work' => '456 Office Rd.'
            ]
        ]);

        // Test __get and __isset
        $this->assertTrue(isset($jsonObject->name));
        $this->assertEquals('John', $jsonObject->name);
        $this->assertTrue(isset($jsonObject->addresses->home));
        $this->assertEquals('123 Main St.', $jsonObject->addresses->home);

        // Test __set
        $jsonObject->email = 'john@example.com';
        $this->assertTrue(isset($jsonObject->email));
        $this->assertEquals('john@example.com', $jsonObject->email);

        $jsonObject->addresses->home = '456 New St.';
        $this->assertEquals('456 New St.', $jsonObject->addresses->home);

        // Test __unset
        unset($jsonObject->age);
        $this->assertFalse(isset($jsonObject->age));

        unset($jsonObject->addresses->work);
        $this->assertFalse(isset($jsonObject->addresses->work));
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonObject::toJson
     *
     * Test that the toJson method returns the correct JSON string representation of the JsonObject.
     *
     * @dataProvider toJsonDataProvider
     * @param array $data
     * @param string $expectedJson
     */
    public function testToJson(array $data, string $expectedJson)
    {
        $jsonObject = new JsonObject($data);
        $this->assertEquals($expectedJson, $jsonObject->toJson());
    }

    /**
     * Data provider for the toJson method.
     *
     * @return array
     */
    public function toJsonDataProvider(): array
    {
        return [
            'empty object' => [[], '{}'],
            'simple object' => [['name' => 'John', 'age' => 30], '{"name":"John","age":30}'],
            'nested object' => [['person' => ['name' => 'Alice', 'age' => 25], 'city' => 'New York'], '{"person":{"name":"Alice","age":25},"city":"New York"}'],
        ];
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonObject::getIterator
     *
     * Test that the getIterator method returns an iterator over the properties of the JsonObject.
     */
    public function testGetIterator()
    {
        $jsonObject = new JsonObject([
            'name' => 'John',
            'age' => 30,
            'addresses' => [
                'home' => '123 Main St.',
                'work' => '456 Office Rd.'
            ]
        ]);

        $iterator = $jsonObject->getIterator();
        $this->assertInstanceOf(\ArrayIterator::class, $iterator);

        $expectedProperties = [
            'name' => 'John',
            'age' => 30,
            'addresses' => [
                'home' => '123 Main St.',
                'work' => '456 Office Rd.'
            ]
        ];

        foreach ($iterator as $key => $value) {
            $this->assertArrayHasKey($key, $expectedProperties);
            $this->assertEquals($expectedProperties[$key], $value);
        }
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonObject
     *
     * Test that the JsonObject class implements the JsonAccessible interface correctly.
     */
    public function testJsonAccessibleImplementation()
    {
        $jsonObject = new JsonObject(['name' => 'John', 'age' => 30]);

        // Test offsetExists
        $this->assertTrue($jsonObject->offsetExists('name'));
        $this->assertFalse($jsonObject->offsetExists('non-existent'));

        // Test offsetGet
        $this->assertEquals('John', $jsonObject->offsetGet('name'));
        $this->assertNull($jsonObject->offsetGet('non-existent'));

        // Test offsetSet
        $jsonObject->offsetSet('email', 'john@example.com');
        $this->assertTrue($jsonObject->offsetExists('email'));
        $this->assertEquals('john@example.com', $jsonObject->offsetGet('email'));

        // Test offsetUnset
        $jsonObject->offsetUnset('age');
        $this->assertFalse($jsonObject->offsetExists('age'));
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonObject
     *
     * Test that the JsonObject class implements the JsonIterable interface correctly.
     */
    public function testJsonIterableImplementation()
    {
        $jsonObject = new JsonObject(['name' => 'John', 'age' => 30]);

        // Test getIterator
        $iterator = $jsonObject->getIterator();
        $this->assertInstanceOf(\Traversable::class, $iterator);

        $expectedProperties = ['name' => 'John', 'age' => 30];
        foreach ($iterator as $key => $value) {
            $this->assertArrayHasKey($key, $expectedProperties);
            $this->assertEquals($expectedProperties[$key], $value);
        }
    }
}