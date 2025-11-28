<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests\Streaming;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Streaming\LazyJsonProxy;

class LazyJsonProxyTest extends TestCase
{
    public function testLazyLoadingDeferred(): void
    {
        $loaded = false;
        $loader = function () use (&$loaded) {
            $loaded = true;
            return ['name' => 'John', 'age' => 30];
        };

        $proxy = new LazyJsonProxy($loader, prefetch: false);
        
        $this->assertFalse($proxy->isLoaded());
        $this->assertFalse($loaded);
        
        $data = $proxy->getData();
        
        $this->assertTrue($proxy->isLoaded());
        $this->assertTrue($loaded);
        $this->assertEquals(['name' => 'John', 'age' => 30], $data);
    }

    public function testLazyLoadingWithPrefetch(): void
    {
        $loaded = false;
        $loader = function () use (&$loaded) {
            $loaded = true;
            return ['test' => 'data'];
        };

        $proxy = new LazyJsonProxy($loader, prefetch: true);
        
        $this->assertTrue($proxy->isLoaded());
        $this->assertTrue($loaded);
    }

    public function testArrayAccess(): void
    {
        $loader = fn () => ['name' => 'John', 'age' => 30];
        $proxy = new LazyJsonProxy($loader);
        
        $this->assertTrue(isset($proxy['name']));
        $this->assertEquals('John', $proxy['name']);
        $this->assertEquals(30, $proxy['age']);
        $this->assertFalse(isset($proxy['nonexistent']));
    }

    public function testMagicPropertyAccess(): void
    {
        $loader = fn () => (object) ['name' => 'John', 'age' => 30];
        $proxy = new LazyJsonProxy($loader);
        
        $this->assertTrue(isset($proxy->name));
        $this->assertEquals('John', $proxy->name);
        $this->assertEquals(30, $proxy->age);
    }

    public function testCountable(): void
    {
        $loader = fn () => ['a', 'b', 'c'];
        $proxy = new LazyJsonProxy($loader);
        
        $this->assertCount(3, $proxy);
    }

    public function testIteratable(): void
    {
        $loader = fn () => ['a' => 1, 'b' => 2, 'c' => 3];
        $proxy = new LazyJsonProxy($loader);
        
        $result = [];
        foreach ($proxy as $key => $value) {
            $result[$key] = $value;
        }
        
        $this->assertEquals(['a' => 1, 'b' => 2, 'c' => 3], $result);
    }

    public function testReset(): void
    {
        $callCount = 0;
        $loader = function () use (&$callCount) {
            $callCount++;
            return ['data' => 'test'];
        };

        $proxy = new LazyJsonProxy($loader);
        
        $proxy->getData();
        $this->assertEquals(1, $callCount);
        $this->assertTrue($proxy->isLoaded());
        
        $proxy->reset();
        $this->assertFalse($proxy->isLoaded());
        
        $proxy->getData();
        $this->assertEquals(2, $callCount);
    }

    public function testArrayOffsetSet(): void
    {
        $loader = fn () => ['name' => 'John'];
        $proxy = new LazyJsonProxy($loader);
        
        $proxy['age'] = 30;
        
        $this->assertEquals(30, $proxy['age']);
    }

    public function testArrayOffsetUnset(): void
    {
        $loader = fn () => ['name' => 'John', 'age' => 30];
        $proxy = new LazyJsonProxy($loader);
        
        unset($proxy['age']);
        
        $this->assertFalse(isset($proxy['age']));
    }

    public function testStringConversion(): void
    {
        $loader = fn () => ['name' => 'John'];
        $proxy = new LazyJsonProxy($loader);
        
        $str = (string) $proxy;
        $decoded = json_decode($str, true);
        
        $this->assertEquals(['name' => 'John'], $decoded);
    }

    public function testObjectDataAccess(): void
    {
        $obj = new \stdClass();
        $obj->name = 'John';
        $obj->age = 30;
        
        $loader = fn () => $obj;
        $proxy = new LazyJsonProxy($loader);
        
        $this->assertEquals('John', $proxy->name);
        $this->assertEquals(30, $proxy->age);
        $this->assertCount(2, $proxy);
    }
}
