<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Json;
use Skpassegna\Json\Exceptions\PathException;

class JsonPointerTest extends TestCase
{
    private Json $json;

    protected function setUp(): void
    {
        $this->json = Json::parse([
            'foo' => ['bar' => 'baz'],
            'numbers' => [1, 2, 3],
            'empty' => null
        ]);
    }

    public function testGetPointer(): void
    {
        $this->assertEquals('baz', $this->json->getPointer('/foo/bar'));
        $this->assertEquals(2, $this->json->getPointer('/numbers/1'));
        $this->assertNull($this->json->getPointer('/empty'));
    }

    public function testSetPointer(): void
    {
        $this->json->setPointer('/foo/bar', 'qux');
        $this->assertEquals('qux', $this->json->getPointer('/foo/bar'));

        $this->json->setPointer('/numbers/1', 42);
        $this->assertEquals(42, $this->json->getPointer('/numbers/1'));

        $this->json->setPointer('/new/path', 'value');
        $this->assertEquals('value', $this->json->getPointer('/new/path'));
    }

    public function testInvalidPointer(): void
    {
        $this->expectException(PathException::class);
        $this->json->getPointer('invalid');
    }

    public function testNonexistentPath(): void
    {
        $this->expectException(PathException::class);
        $this->json->getPointer('/nonexistent/path');
    }
}
