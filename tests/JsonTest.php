<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Json;
use Skpassegna\Json\Exceptions\JsonException;
use Skpassegna\Json\Exceptions\TransformException;

class JsonTest extends TestCase
{
    /**
     * @test
     * @dataProvider validJsonProvider
     */
    public function it_encodes_and_decodes_json($input, string $expected): void
    {
        $json = new Json($input);
        $this->assertEquals($expected, $json->encode());
        $this->assertEquals($input, $json->decode($expected));
    }

    public function validJsonProvider(): array
    {
        return [
            'simple_array' => [
                ['name' => 'John', 'age' => 30],
                '{"name":"John","age":30}'
            ],
            'nested_array' => [
                ['user' => ['name' => 'John', 'age' => 30]],
                '{"user":{"name":"John","age":30}}'
            ],
            'array_with_null' => [
                ['name' => 'John', 'middle' => null],
                '{"name":"John","middle":null}'
            ],
            'array_with_boolean' => [
                ['name' => 'John', 'active' => true],
                '{"name":"John","active":true}'
            ]
        ];
    }

    /**
     * @test
     */
    public function it_handles_json5_input(): void
    {
        $json5 = '{
            // Comment
            name: "John",
            age: 30,
        }';

        $json = new Json();
        $result = $json->fromJson5($json5)->toArray();

        $this->assertEquals([
            'name' => 'John',
            'age' => 30
        ], $result);
    }

    /**
     * @test
     */
    public function it_converts_to_xml(): void
    {
        $data = [
            'user' => [
                'name' => 'John',
                'age' => 30,
                'hobbies' => ['reading', 'gaming']
            ]
        ];

        $json = new Json($data);
        $xml = $json->toXml();

        $this->assertStringContainsString('<user>', $xml);
        $this->assertStringContainsString('<name>John</name>', $xml);
        $this->assertStringContainsString('<age>30</age>', $xml);
        $this->assertStringContainsString('<hobbies>reading</hobbies>', $xml);
    }

    /**
     * @test
     */
    public function it_converts_to_yaml(): void
    {
        $data = [
            'user' => [
                'name' => 'John',
                'age' => 30,
                'hobbies' => ['reading', 'gaming']
            ]
        ];

        $json = new Json($data);
        $yaml = $json->toYaml();

        $this->assertStringContainsString('user:', $yaml);
        $this->assertStringContainsString('name: John', $yaml);
        $this->assertStringContainsString('age: 30', $yaml);
        $this->assertStringContainsString('- reading', $yaml);
    }

    /**
     * @test
     */
    public function it_converts_to_csv(): void
    {
        $data = [
            ['name' => 'John', 'age' => 30],
            ['name' => 'Jane', 'age' => 25]
        ];

        $json = new Json($data);
        $csv = $json->toCsv();

        $this->assertStringContainsString('name,age', $csv);
        $this->assertStringContainsString('John,30', $csv);
        $this->assertStringContainsString('Jane,25', $csv);
    }

    /**
     * @test
     */
    public function it_validates_against_schema(): void
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string'],
                'age' => ['type' => 'integer']
            ],
            'required' => ['name', 'age']
        ];

        $validData = ['name' => 'John', 'age' => 30];
        $invalidData = ['name' => 'John']; // missing required age

        $json = new Json($validData);
        $this->assertTrue($json->validate($schema));

        $json->setData($invalidData);
        $this->assertFalse($json->validate($schema));
    }

    /**
     * @test
     */
    public function it_throws_exception_for_invalid_json(): void
    {
        $this->expectException(JsonException::class);
        $json = new Json();
        $json->decode('{invalid:json}');
    }

    /**
     * @test
     */
    public function it_flattens_nested_arrays(): void
    {
        $data = [
            'user' => [
                'name' => 'John',
                'address' => [
                    'street' => 'Main St',
                    'city' => 'New York'
                ]
            ]
        ];

        $json = new Json($data);
        $flattened = $json->flatten();

        $expected = [
            'user.name' => 'John',
            'user.address.street' => 'Main St',
            'user.address.city' => 'New York'
        ];

        $this->assertEquals($expected, $flattened);
    }

    /**
     * @test
     */
    public function it_unflattens_arrays(): void
    {
        $data = [
            'user.name' => 'John',
            'user.address.street' => 'Main St',
            'user.address.city' => 'New York'
        ];

        $json = new Json();
        $unflattened = $json->unflatten($data);

        $expected = [
            'user' => [
                'name' => 'John',
                'address' => [
                    'street' => 'Main St',
                    'city' => 'New York'
                ]
            ]
        ];

        $this->assertEquals($expected, $unflattened);
    }

    /**
     * @test
     */
    public function it_handles_json_pointer_operations(): void
    {
        $data = [
            'foo' => ['bar' => 'baz'],
            'numbers' => [1, 2, 3]
        ];

        $json = new Json($data);

        $this->assertEquals('baz', $json->get('/foo/bar'));
        $this->assertEquals(2, $json->get('/numbers/1'));

        $json->set('/foo/bar', 'qux');
        $this->assertEquals('qux', $json->get('/foo/bar'));

        $json->remove('/foo/bar');
        $this->assertNull($json->get('/foo/bar'));
    }

    /**
     * @test
     */
    public function it_applies_json_patch_operations(): void
    {
        $data = [
            'foo' => 'bar',
            'numbers' => [1, 2, 3]
        ];

        $patch = [
            ['op' => 'replace', 'path' => '/foo', 'value' => 'baz'],
            ['op' => 'add', 'path' => '/numbers/-', 'value' => 4],
            ['op' => 'remove', 'path' => '/numbers/0']
        ];

        $json = new Json($data);
        $json->patch($patch);

        $this->assertEquals('baz', $json->get('/foo'));
        $this->assertEquals([2, 3, 4], $json->get('/numbers'));
    }

    /**
     * @test
     */
    public function it_handles_jsonpath_queries(): void
    {
        $data = [
            'store' => [
                'books' => [
                    ['title' => 'Book 1', 'price' => 10],
                    ['title' => 'Book 2', 'price' => 20]
                ]
            ]
        ];

        $json = new Json($data);
        
        $titles = $json->query('$.store.books[*].title');
        $this->assertEquals(['Book 1', 'Book 2'], $titles);

        $expensiveBooks = $json->query('$.store.books[?(@.price > 15)].title');
        $this->assertEquals(['Book 2'], $expensiveBooks);
    }
}
