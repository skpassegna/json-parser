<?php

namespace Skpassegna\JsonParser\Tests;

use Skpassegna\JsonParser\JsonArray;
use PHPUnit\Framework\TestCase;

class JsonArrayTest extends TestCase
{
    public function testFilter()
    {
        $data = [1, 2, 3, 4, 5];
        $jsonArray = new JsonArray($data);

        $filtered = $jsonArray->filter(function ($value) {
            return $value % 2 === 0;
        });

        $this->assertEquals([2, 4], $filtered->toArray());
    }

    public function testMap()
    {
        $data = [1, 2, 3, 4, 5];
        $jsonArray = new JsonArray($data);

        $mapped = $jsonArray->map(function ($value) {
            return $value * 2;
        });

        $this->assertEquals([2, 4, 6, 8, 10], $mapped->toArray());
    }

    public function testSort()
    {
        $data = [5, 3, 1, 4, 2];
        $jsonArray = new JsonArray($data);

        $sorted = $jsonArray->sort();
        $this->assertEquals([1, 2, 3, 4, 5], $sorted->toArray());

        $sorted = $jsonArray->sort(function ($a, $b) {
            return $b <=> $a;
        });
        $this->assertEquals([5, 4, 3, 2, 1], $sorted->toArray());
    }

    public function testCount()
    {
        $data = [1, 2, 3, 4, 5];
        $jsonArray = new JsonArray($data);

        $this->assertEquals(5, $jsonArray->count());
    }
}