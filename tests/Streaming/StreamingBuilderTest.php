<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests\Streaming;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Cache\MemoryStore;
use Skpassegna\Json\Streaming\StreamingBuilder;

class StreamingBuilderTest extends TestCase
{
    public function testFluentChaining(): void
    {
        $builder = new StreamingBuilder();
        $result = $builder
            ->withChunkSize(4096)
            ->withBufferLimit(5242880)
            ->withCache()
            ->lazy()
            ->ndJson();
        
        $this->assertInstanceOf(StreamingBuilder::class, $result);
        $this->assertTrue($builder->isLazy());
        $this->assertTrue($builder->isNdJson());
    }

    public function testChunkSizeDefaults(): void
    {
        $builder = new StreamingBuilder();
        $this->assertEquals(8192, $builder->getChunkSize());
    }

    public function testChunkSizeBounds(): void
    {
        $builder = new StreamingBuilder();
        
        $builder->withChunkSize(512);
        $this->assertGreaterThanOrEqual(1024, $builder->getChunkSize());
        
        $builder->withChunkSize(10000000);
        $this->assertLessThanOrEqual(1048576, $builder->getChunkSize());
    }

    public function testBufferLimit(): void
    {
        $builder = new StreamingBuilder();
        $builder->withBufferLimit(20971520);
        
        $this->assertEquals(20971520, $builder->getBufferLimit());
    }

    public function testCaching(): void
    {
        $builder = new StreamingBuilder();
        
        $this->assertNull($builder->getCache());
        
        $builder->withCache();
        $this->assertInstanceOf(MemoryStore::class, $builder->getCache());
        
        $builder->withCache(null, 3600);
        $this->assertEquals(3600, $builder->getCacheTtl());
    }

    public function testCachingDisable(): void
    {
        $builder = new StreamingBuilder();
        
        $builder->withCache();
        $this->assertNotNull($builder->getCache());
        
        $builder->withoutCache();
        $this->assertNull($builder->getCache());
    }

    public function testCustomCacheStore(): void
    {
        $customCache = new MemoryStore();
        $builder = new StreamingBuilder();
        
        $builder->withCache($customCache);
        $this->assertSame($customCache, $builder->getCache());
    }

    public function testLazyMode(): void
    {
        $builder = new StreamingBuilder();
        
        $this->assertFalse($builder->isLazy());
        
        $builder->lazy();
        $this->assertTrue($builder->isLazy());
    }

    public function testNdJsonMode(): void
    {
        $builder = new StreamingBuilder();
        
        $this->assertFalse($builder->isNdJson());
        
        $builder->ndJson();
        $this->assertTrue($builder->isNdJson());
    }

    public function testParserBuilding(): void
    {
        $builder = new StreamingBuilder();
        $parser = $builder->buildParser();
        
        $this->assertNotNull($parser);
    }

    public function testSerializerBuilding(): void
    {
        $builder = new StreamingBuilder();
        $serializer = $builder->buildSerializer();
        
        $this->assertNotNull($serializer);
    }

    public function testProxyCreation(): void
    {
        $builder = new StreamingBuilder();
        $loader = fn () => ['test' => 'data'];
        
        $proxy = $builder->createProxy($loader);
        
        $this->assertNotNull($proxy);
        $this->assertFalse($proxy->isLoaded());
    }

    public function testEventCallbacks(): void
    {
        $chunkCalled = false;
        $errorCalled = false;
        
        $builder = new StreamingBuilder();
        $builder
            ->onChunk(function () use (&$chunkCalled) {
                $chunkCalled = true;
            })
            ->onError(function () use (&$errorCalled) {
                $errorCalled = true;
            });
        
        $parser = $builder->buildParser();
        $this->assertNotNull($parser);
    }
}
