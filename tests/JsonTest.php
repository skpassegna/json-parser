<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Json;
use Skpassegna\Json\Exceptions\ParseException;
use Skpassegna\Json\Exceptions\ValidationException;

class JsonTest extends TestCase
{
    private string $sampleJson = '{"name":"John","age":30,"address":{"city":"New York","country":"USA"},"hobbies":["reading","gaming"]}';

    public function testParse(): void
    {
        $json = Json::parse($this->sampleJson);
        $this->assertEquals('John', $json->get('name'));
        $this->assertEquals(30, $json->get('age'));
    }

    public function testParseInvalidJson(): void
    {
        $this->expectException(ParseException::class);
        Json::parse('{invalid json}');
    }

    public function testGet(): void
    {
        $json = Json::parse($this->sampleJson);
        $this->assertEquals('New York', $json->get('address.city'));
        $this->assertEquals('reading', $json->get('hobbies.0'));
        $this->assertNull($json->get('nonexistent'));
        $this->assertEquals('default', $json->get('nonexistent', 'default'));
    }

    public function testSet(): void
    {
        $json = Json::parse($this->sampleJson);
        $json->set('name', 'Jane')
             ->set('address.city', 'Los Angeles')
             ->set('hobbies.1', 'swimming');

        $this->assertEquals('Jane', $json->get('name'));
        $this->assertEquals('Los Angeles', $json->get('address.city'));
        $this->assertEquals('swimming', $json->get('hobbies.1'));
    }

    public function testRemove(): void
    {
        $json = Json::parse($this->sampleJson);
        $json->remove('age')
             ->remove('address.city');

        $this->assertFalse($json->has('age'));
        $this->assertFalse($json->has('address.city'));
        $this->assertTrue($json->has('address.country'));
    }

    public function testValidateSchema(): void
    {
        $json = Json::parse($this->sampleJson);
        $schema = [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string'],
                'age' => ['type' => 'integer'],
                'address' => [
                    'type' => 'object',
                    'properties' => [
                        'city' => ['type' => 'string'],
                        'country' => ['type' => 'string']
                    ]
                ]
            ],
            'required' => ['name', 'age']
        ];

        $this->assertTrue($json->validateSchema($schema));
    }

    public function testTransformations(): void
    {
        $json = Json::parse($this->sampleJson);

        // Test XML transformation
        $xml = $json->toXml();
        $this->assertStringContainsString('<name>John</name>', $xml);
        $this->assertStringContainsString('<age>30</age>', $xml);

        // Test flattening
        $flattened = $json->flatten();
        $this->assertEquals('New York', $flattened->get('address.city'));
        $this->assertEquals('USA', $flattened->get('address.country'));

        // Test pretty print
        $pretty = $json->prettyPrint();
        $this->assertStringContainsString("\n", $pretty);
        $this->assertStringContainsString("  ", $pretty);

        // Test minify
        $minified = $json->minify();
        $this->assertStringNotContainsString("\n", $minified);
        $this->assertStringNotContainsString("  ", $minified);
    }

    public function testJsonPath(): void
    {
        $json = Json::parse($this->sampleJson);
        
        // Test basic path
        $result = $json->query('$.name');
        $this->assertEquals(['John'], $result);

        // Test nested path
        $result = $json->query('$.address.city');
        $this->assertEquals(['New York'], $result);

        // Test array access
        $result = $json->query('$.hobbies[0]');
        $this->assertEquals(['reading'], $result);

        // Test wildcard
        $result = $json->query('$.hobbies[*]');
        $this->assertEquals(['reading', 'gaming'], $result);
    }

    public function testDataManipulation(): void
    {
        $json = Json::parse('[1, 2, 3, 4, 5]');

        // Test filter
        $filtered = $json->filter(fn($value) => $value > 3);
        $this->assertEquals([3 => 4, 4 => 5], $filtered->toArray());

        // Test map
        $mapped = $json->map(fn($value) => $value * 2);
        $this->assertEquals([2, 4, 6, 8, 10], $mapped->toArray());

        // Test reduce
        $sum = $json->reduce(fn($carry, $value) => $carry + $value, 0);
        $this->assertEquals(15, $sum);
    }

    public function testArrayOperations(): void
    {
        $json = Json::parse('[1, 2, 3, 4, 5]');

        // Test slice
        $sliced = $json->slice(1, 3);
        $this->assertEquals([2, 3, 4], $sliced->values());

        // Test first/last
        $this->assertEquals(1, $json->first());
        $this->assertEquals(5, $json->last());

        // Test find
        $found = $json->find(fn($value) => $value > 3);
        $this->assertEquals([4, 5], $found);
    }
}
