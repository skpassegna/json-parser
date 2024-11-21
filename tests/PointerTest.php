<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Json\Pointer;
use Skpassegna\Json\Exceptions\RuntimeException;

class PointerTest extends TestCase
{
    private Pointer $pointer;

    protected function setUp(): void
    {
        $this->pointer = new Pointer();
    }

    public function testGet(): void
    {
        $document = '{"foo": {"bar": "baz"}, "": 0, "a/b": 1, "c%d": 2, "e^f": 3, "g|h": 4, "i\\\\j": 5, "k\"l": 6, " ": 7, "m~n": 8}';
        
        // Test basic pointer
        $this->assertEquals('baz', $this->pointer->get($document, '/foo/bar'));
        
        // Test empty string key
        $this->assertEquals(0, $this->pointer->get($document, '/'));
        
        // Test special characters
        $this->assertEquals(1, $this->pointer->get($document, '/a~1b'));
        $this->assertEquals(2, $this->pointer->get($document, '/c%d'));
        $this->assertEquals(3, $this->pointer->get($document, '/e^f'));
        $this->assertEquals(4, $this->pointer->get($document, '/g|h'));
        $this->assertEquals(5, $this->pointer->get($document, '/i\\j'));
        $this->assertEquals(6, $this->pointer->get($document, '/k"l'));
        $this->assertEquals(7, $this->pointer->get($document, '/ '));
        $this->assertEquals(8, $this->pointer->get($document, '/m~0n'));
    }

    public function testGetArray(): void
    {
        $document = ['foo' => ['bar' => 'baz']];
        $this->assertEquals('baz', $this->pointer->get($document, '/foo/bar'));
    }

    public function testGetRoot(): void
    {
        $document = '{"foo": "bar"}';
        $this->assertEquals(['foo' => 'bar'], $this->pointer->get($document, ''));
    }

    public function testGetNonexistentPath(): void
    {
        $this->expectException(RuntimeException::class);
        
        $document = '{"foo": "bar"}';
        $this->pointer->get($document, '/baz');
    }

    public function testSet(): void
    {
        $document = '{"foo": "bar"}';
        
        $result = $this->pointer->set($document, '/baz', 'qux');
        $this->assertJsonStringEqualsJsonString('{"foo":"bar","baz":"qux"}', $result);
        
        $result = $this->pointer->set($result, '/foo', 'quux');
        $this->assertJsonStringEqualsJsonString('{"foo":"quux","baz":"qux"}', $result);
    }

    public function testSetArray(): void
    {
        $document = ['foo' => 'bar'];
        $result = $this->pointer->set($document, '/baz', 'qux');
        $this->assertEquals(['foo' => 'bar', 'baz' => 'qux'], $result);
    }

    public function testSetWithMutation(): void
    {
        $document = ['foo' => 'bar'];
        $result = $this->pointer->set($document, '/baz', 'qux', true);
        $this->assertSame($document, $result);
        $this->assertEquals(['foo' => 'bar', 'baz' => 'qux'], $result);
    }

    public function testSetArrayElement(): void
    {
        $document = '{"foo": ["bar", "baz"]}';
        
        $result = $this->pointer->set($document, '/foo/1', 'qux');
        $this->assertJsonStringEqualsJsonString('{"foo":["bar","qux"]}', $result);
        
        $result = $this->pointer->set($result, '/foo/-', 'quux');
        $this->assertJsonStringEqualsJsonString('{"foo":["bar","qux","quux"]}', $result);
    }

    public function testRemove(): void
    {
        $document = '{"foo": "bar", "baz": "qux"}';
        
        $result = $this->pointer->remove($document, '/baz');
        $this->assertJsonStringEqualsJsonString('{"foo":"bar"}', $result);
    }

    public function testRemoveArray(): void
    {
        $document = ['foo' => 'bar', 'baz' => 'qux'];
        $result = $this->pointer->remove($document, '/baz');
        $this->assertEquals(['foo' => 'bar'], $result);
    }

    public function testRemoveWithMutation(): void
    {
        $document = ['foo' => 'bar', 'baz' => 'qux'];
        $result = $this->pointer->remove($document, '/baz', true);
        $this->assertSame($document, $result);
        $this->assertEquals(['foo' => 'bar'], $result);
    }

    public function testRemoveArrayElement(): void
    {
        $document = '{"foo": ["bar", "baz", "qux"]}';
        
        $result = $this->pointer->remove($document, '/foo/1');
        $this->assertJsonStringEqualsJsonString('{"foo":["bar","qux"]}', $result);
    }

    public function testRemoveNonexistentPath(): void
    {
        $this->expectException(RuntimeException::class);
        
        $document = '{"foo": "bar"}';
        $this->pointer->remove($document, '/baz');
    }

    public function testHas(): void
    {
        $document = '{"foo": {"bar": "baz"}}';
        
        $this->assertTrue($this->pointer->has($document, '/foo/bar'));
        $this->assertFalse($this->pointer->has($document, '/foo/qux'));
        $this->assertFalse($this->pointer->has($document, '/baz'));
    }

    public function testCreate(): void
    {
        $this->assertEquals('/foo/bar', $this->pointer->create(['foo', 'bar']));
        $this->assertEquals('/a~1b', $this->pointer->create(['a/b']));
        $this->assertEquals('/m~0n', $this->pointer->create(['m~n']));
        $this->assertEquals('/c%d', $this->pointer->create(['c%d']));
        $this->assertEquals('/', $this->pointer->create(['']));
    }
}
