<?php

namespace Skpassegna\JsonParser\Tests;

use Skpassegna\JsonParser\JsonArray;
use PHPUnit\Framework\TestCase;

class JsonArrayTest extends TestCase
{
    /**
     * @covers \Skpassegna\JsonParser\JsonArray::filter
     *
     * Test the filter method for filtering elements in a JsonArray.
     *
     * @dataProvider filterDataProvider
     * @param array $data
     * @param callable $callback
     * @param array $expectedResult
     */
    public function testFilter(array $data, callable $callback, array $expectedResult)
    {
        $jsonArray = new JsonArray($data);
        $filteredArray = $jsonArray->filter($callback);

        $this->assertInstanceOf(JsonArray::class, $filteredArray);
        $this->assertEquals($expectedResult, $filteredArray->toArray());
    }

    /**
     * Data provider for the filter method.
     *
     * @return array
     */
    public function filterDataProvider(): array
    {
        return [
            'filter even numbers' => [
                [1, 2, 3, 4, 5],
                function ($value) {
                    return $value % 2 === 0;
                },
                [2, 4]
            ],
            'filter odd numbers' => [
                [1, 2, 3, 4, 5],
                function ($value) {
                    return $value % 2 !== 0;
                },
                [1, 3, 5]
            ],
            'filter empty array' => [
                [],
                function ($value) {
                    return true;
                },
                []
            ],
        ];
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonArray::map
     *
     * Test the map method for mapping elements in a JsonArray.
     *
     * @dataProvider mapDataProvider
     * @param array $data
     * @param callable $callback
     * @param array $expectedResult
     */
    public function testMap(array $data, callable $callback, array $expectedResult)
    {
        $jsonArray = new JsonArray($data);
        $mappedArray = $jsonArray->map($callback);

        $this->assertInstanceOf(JsonArray::class, $mappedArray);
        $this->assertEquals($expectedResult, $mappedArray->toArray());
    }

    /**
     * Data provider for the map method.
     *
     * @return array
     */
    public function mapDataProvider(): array
    {
        return [
            'double numbers' => [
                [1, 2, 3, 4, 5],
                function ($value) {
                    return $value * 2;
                },
                [2, 4, 6, 8, 10]
            ],
            'square numbers' => [
                [1, 2, 3, 4, 5],
                function ($value) {
                    return $value ** 2;
                },
                [1, 4, 9, 16, 25]
            ],
            'map empty array' => [
                [],
                function ($value) {
                    return $value * 2;
                },
                []
            ],
        ];
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonArray::sort
     *
     * Test the sort method for sorting elements in a JsonArray.
     *
     * @dataProvider sortDataProvider
     * @param array $data
     * @param callable|null $callback
     * @param array $expectedResult
     */
    public function testSort(array $data, ?callable $callback, array $expectedResult)
    {
        $jsonArray = new JsonArray($data);
        $sortedArray = $jsonArray->sort($callback);

        $this->assertInstanceOf(JsonArray::class, $sortedArray);
        $this->assertEquals($expectedResult, $sortedArray->toArray());
    }

    /**
     * Data provider for the sort method.
     *
     * @return array
     */
    public function sortDataProvider(): array
    {
        return [
            'sort numbers ascending' => [
                [5, 2, 8, 1, 3],
                null,
                [1, 2, 3, 5, 8]
            ],
            'sort numbers descending' => [
                [5, 2, 8, 1, 3],
                function ($a, $b) {
                    return $b <=> $a;
                },
                [8, 5, 3, 2, 1]
            ],
            'sort strings' => [
                ['apple', 'banana', 'cherry', 'date'],
                null,
                ['apple', 'banana', 'cherry', 'date']
            ],
            'sort empty array' => [
                [],
                null,
                []
            ],
        ];
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonArray::count
     *
     * Test the count method for getting the number of elements in a JsonArray.
     *
     * @dataProvider countDataProvider
     * @param array $data
     * @param int $expectedCount
     */
    public function testCount(array $data, int $expectedCount)
    {
        $jsonArray = new JsonArray($data);
        $this->assertEquals($expectedCount, $jsonArray->count());
    }

    /**
     * Data provider for the count method.
     *
     * @return array
     */
    public function countDataProvider(): array
    {
        return [
            'count zero elements' => [[], 0],
            'count some elements' => [[1, 2, 3, 4, 5], 5],
            'count nested elements' => [[[1, 2], [3, 4], [5, 6]], 3],
        ];
    }


    /**
     * @covers \Skpassegna\JsonParser\JsonArray
     *
     * Test that the JsonArray class implements the JsonAccessible interface correctly.
     */
    public function testJsonAccessibleImplementation()
    {
        $jsonArray = new JsonArray([1, 2, 3]);

        // Test offsetExists
        $this->assertTrue($jsonArray->offsetExists(0));
        $this->assertFalse($jsonArray->offsetExists(10));

        // Test offsetGet
        $this->assertEquals(1, $jsonArray->offsetGet(0));
        $this->assertNull($jsonArray->offsetGet(10));

        // Test offsetSet
        $jsonArray->offsetSet(3, 4);
        $this->assertTrue($jsonArray->offsetExists(3));
        $this->assertEquals(4, $jsonArray->offsetGet(3));

        // Test offsetUnset
        $jsonArray->offsetUnset(1);
        $this->assertFalse($jsonArray->offsetExists(1));
    }

    /**
     * @covers \Skpassegna\JsonParser\JsonArray
     *
     * Test that the JsonArray class implements the JsonIterable interface correctly.
     */
    public function testJsonIterableImplementation()
    {
        $jsonArray = new JsonArray([1, 2, 3]);

        // Test getIterator
        $iterator = $jsonArray->getIterator();
        $this->assertInstanceOf(\Traversable::class, $iterator);

        $expectedValues = [1, 2, 3];
        foreach ($iterator as $key => $value) {
            $this->assertEquals($expectedValues[$key], $value);
        }
    }
}