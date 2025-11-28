<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests\Streaming;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Cache\MemoryStore;
use Skpassegna\Json\Json;
use Skpassegna\Json\Streaming\LazyJsonProxy;

class IntegrationTest extends TestCase
{
    public function testStreamingWithCaching(): void
    {
        $json = Json::parse([
            'users' => [
                ['id' => 1, 'name' => 'Alice', 'age' => 30],
                ['id' => 2, 'name' => 'Bob', 'age' => 25],
                ['id' => 3, 'name' => 'Charlie', 'age' => 35],
            ],
        ]);

        $cache = new MemoryStore();

        // First query - cache miss
        $results1 = $json->queryWithCache('$.users[*]', $cache);
        $this->assertCount(3, $results1);

        // Second query - cache hit
        $results2 = $json->queryWithCache('$.users[*]', $cache);
        $this->assertCount(3, $results2);
        $this->assertEquals($results1, $results2);
    }

    public function testLazyProxyWithBuilderConfiguration(): void
    {
        $builder = Json::streaming()->lazy();

        $loader = function () {
            return ['data' => 'loaded'];
        };

        $proxy = $builder->createProxy($loader, prefetch: false);

        $this->assertFalse($proxy->isLoaded());
        $this->assertEquals('loaded', $proxy['data']);
        $this->assertTrue($proxy->isLoaded());
    }

    public function testLazyProxyIterationAndCounting(): void
    {
        $data = [
            'name' => 'John',
            'age' => 30,
            'email' => 'john@example.com',
        ];

        $lazy = Json::lazy(fn () => $data);

        $this->assertCount(3, $lazy);

        $keys = [];
        foreach ($lazy as $key => $value) {
            $keys[] = $key;
        }

        $this->assertEquals(['name', 'age', 'email'], $keys);
    }

    public function testCacheWithDifferentDataTypes(): void
    {
        $cache = new MemoryStore();

        // String
        $cache->put('string', 'value');
        $this->assertEquals('value', $cache->get('string'));

        // Array
        $cache->put('array', ['a' => 1, 'b' => 2]);
        $this->assertEquals(['a' => 1, 'b' => 2], $cache->get('array'));

        // Object
        $obj = new \stdClass();
        $obj->prop = 'test';
        $cache->put('object', $obj);
        $retrieved = $cache->get('object');
        $this->assertEquals('test', $retrieved->prop);

        // Null
        $cache->put('null', null);
        $this->assertNull($cache->get('null'));
    }

    public function testJsonFacadeStaticMethods(): void
    {
        // Test static factory methods
        $builder = Json::streaming();
        $this->assertNotNull($builder);

        $lazy = Json::lazy(fn () => ['test' => true]);
        $this->assertInstanceOf(LazyJsonProxy::class, $lazy);

        $cache = Json::cache();
        $this->assertNotNull($cache);
    }

    public function testBuilderModeConfigurations(): void
    {
        // Test builder with different configurations
        $builder1 = Json::streaming()
            ->withChunkSize(4096)
            ->withBufferLimit(20971520);

        $this->assertEquals(4096, $builder1->getChunkSize());
        $this->assertEquals(20971520, $builder1->getBufferLimit());

        // Test with cache
        $builder2 = Json::streaming()
            ->withCache()
            ->withCache(null, 7200);

        $this->assertNotNull($builder2->getCache());
        $this->assertEquals(7200, $builder2->getCacheTtl());

        // Test modes
        $builder3 = Json::streaming()->lazy();
        $this->assertTrue($builder3->isLazy());

        $builder4 = Json::streaming()->ndJson();
        $this->assertTrue($builder4->isNdJson());
    }

    public function testLazyProxyReset(): void
    {
        $loadCount = 0;
        $loader = function () use (&$loadCount) {
            $loadCount++;
            return ['count' => $loadCount];
        };

        $lazy = Json::lazy($loader);

        $lazy->getData();
        $this->assertEquals(1, $loadCount);

        $lazy->reset();
        $this->assertFalse($lazy->isLoaded());

        $lazy->getData();
        $this->assertEquals(2, $loadCount);
    }

    public function testCacheInvalidation(): void
    {
        $cache = new MemoryStore();

        $cache->put('key1', 'value1');
        $cache->put('key2', 'value2');

        $this->assertTrue($cache->has('key1'));
        $this->assertTrue($cache->has('key2'));

        $cache->forget('key1');
        $this->assertFalse($cache->has('key1'));
        $this->assertTrue($cache->has('key2'));

        $cache->flush();
        $this->assertFalse($cache->has('key1'));
        $this->assertFalse($cache->has('key2'));
    }

    public function testLazyProxyArrayOffsetOperations(): void
    {
        $lazy = Json::lazy(fn () => ['a' => 1, 'b' => 2]);

        // Read
        $this->assertEquals(1, $lazy['a']);

        // Write
        $lazy['c'] = 3;
        $this->assertEquals(3, $lazy['c']);

        // Unset
        unset($lazy['a']);
        $this->assertFalse(isset($lazy['a']));
    }

    public function testLazyProxyWithObjectData(): void
    {
        $obj = new \stdClass();
        $obj->name = 'Test';
        $obj->value = 42;

        $lazy = Json::lazy(fn () => $obj);

        $this->assertEquals('Test', $lazy->name);
        $this->assertEquals(42, $lazy->value);
        $this->assertCount(2, $lazy);

        foreach ($lazy as $key => $value) {
            $this->assertContains($key, ['name', 'value']);
        }
    }

    public function testCacheWithTtlExpiration(): void
    {
        $cache = new MemoryStore();

        $cache->put('temp', 'value', ttl: 1);
        $this->assertTrue($cache->has('temp'));

        sleep(2);

        $this->assertFalse($cache->has('temp'));
        $this->assertNull($cache->get('temp'));
    }
}
