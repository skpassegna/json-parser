<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Json;
use Skpassegna\Json\JsonMutabilityMode;
use Skpassegna\Json\Exceptions\RuntimeException;
use ArrayIterator;

class MagicMethodsAndInterfacesTest extends TestCase
{
    /**
     * @test
     */
    public function it_implements_array_access(): void
    {
        $json = new Json(['name' => 'John', 'age' => 30]);

        $this->assertTrue(isset($json['name']));
        $this->assertEquals('John', $json['name']);
        $this->assertFalse(isset($json['unknown']));
        $this->assertNull($json['unknown']);
    }

    /**
     * @test
     */
    public function it_can_set_values_via_array_access(): void
    {
        $json = new Json(['name' => 'John']);

        $json['age'] = 30;
        $this->assertEquals(30, $json['age']);
        $this->assertEquals(30, $json->get('age'));
    }

    /**
     * @test
     */
    public function it_can_unset_values_via_array_access(): void
    {
        $json = new Json(['name' => 'John', 'age' => 30]);

        unset($json['age']);
        $this->assertFalse(isset($json['age']));
    }

    /**
     * @test
     */
    public function it_throws_when_setting_via_array_access_in_immutable_mode(): void
    {
        $json = new Json(['name' => 'John'], JsonMutabilityMode::IMMUTABLE);

        $this->expectException(RuntimeException::class);
        $json['age'] = 30;
    }

    /**
     * @test
     */
    public function it_throws_when_unsetting_via_array_access_in_immutable_mode(): void
    {
        $json = new Json(['name' => 'John'], JsonMutabilityMode::IMMUTABLE);

        $this->expectException(RuntimeException::class);
        unset($json['name']);
    }

    /**
     * @test
     */
    public function it_implements_iterator_aggregate(): void
    {
        $data = ['a' => 1, 'b' => 2, 'c' => 3];
        $json = new Json($data);

        $iterator = $json->getIterator();
        $this->assertInstanceOf(ArrayIterator::class, $iterator);

        $result = [];
        foreach ($json as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals($data, $result);
    }

    /**
     * @test
     */
    public function it_implements_countable(): void
    {
        $json = new Json(['a' => 1, 'b' => 2, 'c' => 3]);

        $this->assertEquals(3, count($json));
        $this->assertFalse($json->isEmpty());
    }

    /**
     * @test
     */
    public function it_implements_stringable(): void
    {
        $data = ['name' => 'John', 'age' => 30];
        $json = new Json($data);

        $string = (string)$json;
        $this->assertIsString($string);
        $this->assertStringContainsString('name', $string);
        $this->assertStringContainsString('John', $string);
    }

    /**
     * @test
     */
    public function it_supports_magic_get(): void
    {
        $json = new Json(['name' => 'John', 'email' => 'john@example.com']);

        $this->assertEquals('John', $json->name);
        $this->assertEquals('john@example.com', $json->email);
    }

    /**
     * @test
     */
    public function it_supports_magic_set(): void
    {
        $json = new Json([]);

        $json->name = 'John';
        $json->age = 30;

        $this->assertEquals('John', $json->name);
        $this->assertEquals(30, $json->age);
    }

    /**
     * @test
     */
    public function it_supports_magic_isset(): void
    {
        $json = new Json(['name' => 'John']);

        $this->assertTrue(isset($json->name));
        $this->assertFalse(isset($json->email));
    }

    /**
     * @test
     */
    public function it_supports_magic_unset(): void
    {
        $json = new Json(['name' => 'John', 'age' => 30]);

        unset($json->age);

        $this->assertTrue(isset($json->name));
        $this->assertFalse(isset($json->age));
    }

    /**
     * @test
     */
    public function it_throws_when_magic_set_in_immutable_mode(): void
    {
        $json = new Json(['name' => 'John'], JsonMutabilityMode::IMMUTABLE);

        $this->expectException(RuntimeException::class);
        $json->age = 30;
    }

    /**
     * @test
     */
    public function it_throws_when_magic_unset_in_immutable_mode(): void
    {
        $json = new Json(['name' => 'John'], JsonMutabilityMode::IMMUTABLE);

        $this->expectException(RuntimeException::class);
        unset($json->name);
    }

    /**
     * @test
     */
    public function it_supports_invoke_magic_method(): void
    {
        $json = new Json(['name' => 'John', 'user' => ['email' => 'john@example.com']]);

        $this->assertEquals($json->getData(), $json());
        $this->assertEquals('John', $json('name'));
        $this->assertEquals('john@example.com', $json('user.email'));
    }

    /**
     * @test
     */
    public function it_supports_to_string_magic_method(): void
    {
        $json = new Json(['name' => 'John']);

        $stringified = (string)$json;
        $this->assertIsString($stringified);
        $this->assertEquals($json->toString(), $stringified);
    }

    /**
     * @test
     */
    public function it_supports_clone_magic_method(): void
    {
        $original = new Json(['name' => 'John', 'age' => 30], JsonMutabilityMode::IMMUTABLE);

        $cloned = clone $original;

        $this->assertEquals($original->getData(), $cloned->getData());
        $this->assertTrue($cloned->isMutable());
        $this->assertTrue($original->isImmutable());
    }

    /**
     * @test
     */
    public function it_supports_debug_info_magic_method(): void
    {
        $json = new Json(['name' => 'John'], JsonMutabilityMode::MUTABLE);

        $debugInfo = $json->__debugInfo();

        $this->assertIsArray($debugInfo);
        $this->assertArrayHasKey('data', $debugInfo);
        $this->assertArrayHasKey('mutabilityMode', $debugInfo);
        $this->assertEquals('MUTABLE', $debugInfo['mutabilityMode']);
    }

    /**
     * @test
     */
    public function it_supports_serialization_magic_methods(): void
    {
        $original = new Json(['name' => 'John', 'age' => 30], JsonMutabilityMode::IMMUTABLE);

        $serialized = serialize($original);
        $unserialized = unserialize($serialized);

        $this->assertEquals($original->getData(), $unserialized->getData());
        $this->assertEquals($original->getMutabilityMode(), $unserialized->getMutabilityMode());
        $this->assertTrue($unserialized->isImmutable());
    }

    /**
     * @test
     */
    public function it_throws_on_undefined_method(): void
    {
        $json = new Json([]);

        $this->expectException(RuntimeException::class);
        $json->undefinedMethod();
    }

    /**
     * @test
     */
    public function it_throws_on_undefined_static_method(): void
    {
        $this->expectException(RuntimeException::class);
        Json::undefinedStaticMethod();
    }

    /**
     * @test
     */
    public function it_manages_mutability_mode(): void
    {
        $json = new Json(['name' => 'John']);

        $this->assertTrue($json->isMutable());
        $this->assertFalse($json->isImmutable());

        $json->setMutabilityMode(JsonMutabilityMode::IMMUTABLE);

        $this->assertFalse($json->isMutable());
        $this->assertTrue($json->isImmutable());

        $this->expectException(RuntimeException::class);
        $json->set('age', 30);
    }

    /**
     * @test
     */
    public function it_protects_mutations_in_immutable_mode_for_all_mutators(): void
    {
        $json = new Json(['a' => 1, 'b' => 2], JsonMutabilityMode::IMMUTABLE);

        $this->expectException(RuntimeException::class);
        $json->remove('a');
    }

    /**
     * @test
     */
    public function it_protects_merge_in_immutable_mode(): void
    {
        $json = new Json(['a' => 1], JsonMutabilityMode::IMMUTABLE);

        $this->expectException(RuntimeException::class);
        $json->merge(['b' => 2]);
    }

    /**
     * @test
     */
    public function it_protects_set_pointer_in_immutable_mode(): void
    {
        $json = new Json(['a' => ['b' => 1]], JsonMutabilityMode::IMMUTABLE);

        $this->expectException(RuntimeException::class);
        $json->setPointer('/a/b', 2);
    }

    /**
     * @test
     */
    public function it_protects_merge_json_in_immutable_mode(): void
    {
        $json = new Json(['a' => 1], JsonMutabilityMode::IMMUTABLE);

        $this->expectException(RuntimeException::class);
        $json->mergeJson(['b' => 2]);
    }

    /**
     * @test
     */
    public function it_allows_read_operations_in_immutable_mode(): void
    {
        $data = ['name' => 'John', 'age' => 30];
        $json = new Json($data, JsonMutabilityMode::IMMUTABLE);

        $this->assertEquals('John', $json->get('name'));
        $this->assertTrue($json->has('name'));
        $this->assertEquals($data, $json->getData());
        $this->assertEquals(2, count($json));

        $found = 0;
        foreach ($json as $value) {
            $found++;
        }
        $this->assertEquals(2, $found);
    }

    /**
     * @test
     */
    public function it_can_append_to_array_via_array_access(): void
    {
        $json = new Json([1, 2, 3]);

        $json[] = 4;

        $this->assertEquals(4, $json[3]);
        $this->assertEquals(4, count($json));
    }

    /**
     * @test
     */
    public function it_prevents_append_in_immutable_mode(): void
    {
        $json = new Json([1, 2, 3], JsonMutabilityMode::IMMUTABLE);

        $this->expectException(RuntimeException::class);
        $json[] = 4;
    }

    /**
     * @test
     */
    public function it_works_with_object_data(): void
    {
        $obj = new \stdClass();
        $obj->name = 'John';
        $obj->age = 30;

        $json = new Json($obj);

        $this->assertTrue(isset($json['name']));
        $this->assertEquals('John', $json['name']);

        $arrayVersion = (array)$obj;
        $this->assertEquals(count($arrayVersion), count($json));

        $iterationCount = 0;
        $keys = [];
        foreach ($json as $key => $value) {
            $keys[] = $key;
            $iterationCount++;
        }
        $this->assertEquals(2, $iterationCount);
        $this->assertContains('name', $keys);
        $this->assertContains('age', $keys);
    }

    /**
     * @test
     */
    public function it_handles_nested_property_access_via_magic_methods(): void
    {
        $json = new Json(['user' => ['profile' => ['name' => 'John']]]);

        $this->assertEquals('John', $json->get('user.profile.name'));
        $this->assertTrue(isset($json->{'user'}));
    }

    /**
     * @test
     */
    public function it_returns_zero_count_for_empty_json(): void
    {
        $json = new Json([]);

        $this->assertEquals(0, count($json));
    }

    /**
     * @test
     */
    public function it_implements_all_interfaces(): void
    {
        $json = new Json([]);

        $this->assertInstanceOf(\ArrayAccess::class, $json);
        $this->assertInstanceOf(\IteratorAggregate::class, $json);
        $this->assertInstanceOf(\Countable::class, $json);
        $this->assertInstanceOf(\Stringable::class, $json);
    }
}
