<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests\Streaming;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Cache\MemoryStore;

class CacheStoreTest extends TestCase
{
    private MemoryStore $cache;

    protected function setUp(): void
    {
        $this->cache = new MemoryStore();
    }

    public function testPutAndGet(): void
    {
        $this->assertTrue($this->cache->put('key1', 'value1'));
        $this->assertEquals('value1', $this->cache->get('key1'));
    }

    public function testGetNonexistent(): void
    {
        $this->assertNull($this->cache->get('nonexistent'));
        $this->assertEquals('default', $this->cache->get('nonexistent', 'default'));
    }

    public function testHas(): void
    {
        $this->cache->put('key1', 'value1');
        
        $this->assertTrue($this->cache->has('key1'));
        $this->assertFalse($this->cache->has('nonexistent'));
    }

    public function testForget(): void
    {
        $this->cache->put('key1', 'value1');
        $this->assertTrue($this->cache->has('key1'));
        
        $this->assertTrue($this->cache->forget('key1'));
        $this->assertFalse($this->cache->has('key1'));
    }

    public function testFlush(): void
    {
        $this->cache->put('key1', 'value1');
        $this->cache->put('key2', 'value2');
        
        $this->assertTrue($this->cache->flush());
        
        $this->assertFalse($this->cache->has('key1'));
        $this->assertFalse($this->cache->has('key2'));
    }

    public function testTtlExpiration(): void
    {
        $this->cache->put('key1', 'value1', 1);
        $this->assertTrue($this->cache->has('key1'));
        
        sleep(2);
        
        $this->assertFalse($this->cache->has('key1'));
        $this->assertNull($this->cache->get('key1'));
    }

    public function testPutArray(): void
    {
        $array = ['a' => 1, 'b' => 2];
        $this->cache->put('array', $array);
        
        $retrieved = $this->cache->get('array');
        $this->assertEquals($array, $retrieved);
    }

    public function testPutObject(): void
    {
        $obj = new \stdClass();
        $obj->prop = 'value';
        
        $this->cache->put('object', $obj);
        
        $retrieved = $this->cache->get('object');
        $this->assertInstanceOf(\stdClass::class, $retrieved);
        $this->assertEquals('value', $retrieved->prop);
    }

    public function testMultipleKeys(): void
    {
        $this->cache->put('key1', 'value1');
        $this->cache->put('key2', 'value2');
        $this->cache->put('key3', 'value3');
        
        $this->assertEquals('value1', $this->cache->get('key1'));
        $this->assertEquals('value2', $this->cache->get('key2'));
        $this->assertEquals('value3', $this->cache->get('key3'));
    }

    public function testForgetNonexistent(): void
    {
        $this->assertFalse($this->cache->forget('nonexistent'));
    }
}
