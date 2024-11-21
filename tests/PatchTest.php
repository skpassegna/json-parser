<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Json\Patch;
use Skpassegna\Json\Exceptions\RuntimeException;

class PatchTest extends TestCase
{
    private Patch $patch;

    protected function setUp(): void
    {
        $this->patch = new Patch();
    }

    public function testApplyAdd(): void
    {
        $document = '{"foo": "bar"}';
        $patch = '[{"op": "add", "path": "/baz", "value": "qux"}]';
        
        $result = $this->patch->apply($document, $patch);
        $expected = '{"foo":"bar","baz":"qux"}';
        
        $this->assertJsonStringEqualsJsonString($expected, $result);
    }

    public function testApplyRemove(): void
    {
        $document = '{"foo": "bar", "baz": "qux"}';
        $patch = '[{"op": "remove", "path": "/baz"}]';
        
        $result = $this->patch->apply($document, $patch);
        $expected = '{"foo":"bar"}';
        
        $this->assertJsonStringEqualsJsonString($expected, $result);
    }

    public function testApplyReplace(): void
    {
        $document = '{"foo": "bar", "baz": "qux"}';
        $patch = '[{"op": "replace", "path": "/baz", "value": "quux"}]';
        
        $result = $this->patch->apply($document, $patch);
        $expected = '{"foo":"bar","baz":"quux"}';
        
        $this->assertJsonStringEqualsJsonString($expected, $result);
    }

    public function testApplyMove(): void
    {
        $document = '{"foo": {"bar": "baz"}, "qux": {"corge": "grault"}}';
        $patch = '[{"op": "move", "from": "/foo/bar", "path": "/qux/thud"}]';
        
        $result = $this->patch->apply($document, $patch);
        $expected = '{"foo":{},"qux":{"corge":"grault","thud":"baz"}}';
        
        $this->assertJsonStringEqualsJsonString($expected, $result);
    }

    public function testApplyCopy(): void
    {
        $document = '{"foo": {"bar": "baz"}}';
        $patch = '[{"op": "copy", "from": "/foo/bar", "path": "/baz"}]';
        
        $result = $this->patch->apply($document, $patch);
        $expected = '{"foo":{"bar":"baz"},"baz":"baz"}';
        
        $this->assertJsonStringEqualsJsonString($expected, $result);
    }

    public function testApplyTest(): void
    {
        $document = '{"foo": {"bar": "baz"}}';
        $patch = '[{"op": "test", "path": "/foo/bar", "value": "baz"}]';
        
        $result = $this->patch->apply($document, $patch);
        $this->assertJsonStringEqualsJsonString($document, $result);
    }

    public function testApplyTestFailure(): void
    {
        $this->expectException(RuntimeException::class);
        
        $document = '{"foo": {"bar": "baz"}}';
        $patch = '[{"op": "test", "path": "/foo/bar", "value": "qux"}]';
        
        $this->patch->apply($document, $patch);
    }

    public function testApplyInvalidOperation(): void
    {
        $this->expectException(RuntimeException::class);
        
        $document = '{"foo": "bar"}';
        $patch = '[{"op": "invalid", "path": "/baz", "value": "qux"}]';
        
        $this->patch->apply($document, $patch);
    }

    public function testDiff(): void
    {
        $source = '{"foo":"bar","numbers":[1,2,3]}';
        $target = '{"foo":"baz","numbers":[1,3],"extra":"field"}';
        
        $patch = $this->patch->diff($source, $target);
        $result = $this->patch->apply($source, $patch);
        
        $this->assertJsonStringEqualsJsonString($target, $result);
    }

    public function testTest(): void
    {
        $document = '{"foo": "bar"}';
        $validPatch = '[{"op": "test", "path": "/foo", "value": "bar"}]';
        $invalidPatch = '[{"op": "test", "path": "/foo", "value": "baz"}]';
        
        $this->assertTrue($this->patch->test($document, $validPatch));
        $this->assertFalse($this->patch->test($document, $invalidPatch));
    }
}
