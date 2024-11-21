<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Json;
use Skpassegna\Json\Utils\JsonMerge;

class JsonMergeTest extends TestCase
{
    private Json $json;

    protected function setUp(): void
    {
        $this->json = Json::parse([
            'name' => 'John',
            'age' => 30,
            'address' => [
                'street' => '123 Main St',
                'city' => 'New York'
            ],
            'hobbies' => ['reading', 'gaming']
        ]);
    }

    public function testMergeReplace(): void
    {
        $source = [
            'name' => 'Jane',
            'address' => [
                'country' => 'USA'
            ]
        ];

        $this->json->mergeJson($source, JsonMerge::MERGE_REPLACE);
        
        $this->assertEquals('Jane', $this->json->get('name'));
        $this->assertArrayNotHasKey('street', $this->json->get('address'));
        $this->assertEquals('USA', $this->json->get('address.country'));
    }

    public function testMergeRecursive(): void
    {
        $source = [
            'name' => 'Jane',
            'address' => [
                'country' => 'USA'
            ],
            'hobbies' => ['swimming']
        ];

        $this->json->mergeJson($source, JsonMerge::MERGE_RECURSIVE);
        
        $this->assertEquals('Jane', $this->json->get('name'));
        $this->assertEquals('123 Main St', $this->json->get('address.street'));
        $this->assertEquals('USA', $this->json->get('address.country'));
        $this->assertContains('swimming', $this->json->get('hobbies'));
    }

    public function testMergeDistinct(): void
    {
        $source = [
            'name' => 'Jane',
            'address' => [
                'street' => '456 Oak St',
                'country' => 'USA'
            ],
            'email' => 'jane@example.com'
        ];

        $this->json->mergeJson($source, JsonMerge::MERGE_DISTINCT);
        
        $this->assertEquals('John', $this->json->get('name')); // Original value preserved
        $this->assertEquals('123 Main St', $this->json->get('address.street')); // Original value preserved
        $this->assertEquals('USA', $this->json->get('address.country')); // New value added
        $this->assertEquals('jane@example.com', $this->json->get('email')); // New value added
    }

    public function testMergeWithScalarValues(): void
    {
        $original = Json::parse(['value' => 42]);
        $source = ['value' => 'string'];

        $original->mergeJson($source);
        $this->assertEquals('string', $original->get('value'));
    }

    public function testMergeWithNestedArrays(): void
    {
        $source = [
            'nested' => [
                'arrays' => [
                    'deep' => [1, 2, 3]
                ]
            ]
        ];

        $this->json->mergeJson($source);
        $this->assertEquals([1, 2, 3], $this->json->get('nested.arrays.deep'));
    }
}
