<?php

declare(strict_types=1);

namespace Skpassegna\Json\Benchmarks;

use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use Psr\Http\Message\StreamInterface;
use Skpassegna\Json\Cache\MemoryStore;
use Skpassegna\Json\Json;

/**
 * Benchmark streaming and lazy loading functionality
 * 
 * Run with: vendor/bin/phpbench run benchmarks/StreamingBench.php
 */
class SimpleStream implements StreamInterface
{
    private string $content;
    private int $position = 0;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function read($length): string
    {
        $chunk = substr($this->content, $this->position, $length);
        $this->position += strlen($chunk);
        return $chunk;
    }

    public function eof(): bool
    {
        return $this->position >= strlen($this->content);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function __toString(): string { return $this->content; }
    public function getContents(): string { return $this->content; }
    public function tell(): int { return $this->position; }
    public function isReadable(): bool { return true; }
    public function isWritable(): bool { return false; }
    public function isSeekable(): bool { return true; }
    public function seek($offset, $whence = SEEK_SET): void { $this->position = $offset; }
    public function write($string): int { return 0; }
    public function getSize(): ?int { return strlen($this->content); }
    public function getMetadata($key = null) { return null; }
    public function close(): void {}
    public function detach() { return null; }
}

class StreamingBench
{
    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchStreamingSmallPayload(): void
    {
        $json = json_encode(array_fill(0, 10, ['id' => 1, 'name' => 'test']));
        $stream = new SimpleStream($json);

        $count = 0;
        foreach (Json::parseStream($stream, chunkSize: 1024) as $item) {
            $count++;
        }
    }

    /**
     * @Revs(50)
     * @Iterations(5)
     */
    public function benchStreamingMediumPayload(): void
    {
        $json = json_encode(array_fill(0, 100, ['id' => 1, 'name' => 'test', 'data' => str_repeat('x', 100)]));
        $stream = new SimpleStream($json);

        $count = 0;
        foreach (Json::parseStream($stream, chunkSize: 2048) as $item) {
            $count++;
        }
    }

    /**
     * @Revs(10)
     * @Iterations(3)
     */
    public function benchStreamingLargePayload(): void
    {
        $items = array_fill(0, 1000, ['id' => 1, 'name' => 'test', 'data' => str_repeat('x', 50)]);
        $json = json_encode($items);
        $stream = new SimpleStream($json);

        $count = 0;
        foreach (Json::parseStream($stream, chunkSize: 4096) as $item) {
            $count++;
        }
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchNdJsonParsing(): void
    {
        $lines = array_map(fn ($i) => json_encode(['id' => $i, 'name' => 'user' . $i]), range(1, 50));
        $ndjson = implode("\n", $lines) . "\n";
        $stream = new SimpleStream($ndjson);

        $count = 0;
        foreach (Json::parseNdJsonStream($stream, chunkSize: 1024) as $item) {
            $count++;
        }
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchLazyLoadingDeferred(): void
    {
        $loader = fn () => ['data' => array_fill(0, 100, ['id' => 1, 'name' => 'test'])];
        $lazy = Json::lazy($loader, prefetch: false);

        $data = $lazy['data'];
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchLazyLoadingPrefetch(): void
    {
        $loader = fn () => ['data' => array_fill(0, 100, ['id' => 1, 'name' => 'test'])];
        $lazy = Json::lazy($loader, prefetch: true);

        $data = $lazy['data'];
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchCacheHit(): void
    {
        $cache = new MemoryStore();
        $cache->put('key', ['data' => 'value']);

        $value = $cache->get('key');
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchCacheMiss(): void
    {
        $cache = new MemoryStore();

        $value = $cache->get('nonexistent', 'default');
    }

    /**
     * @Revs(50)
     * @Iterations(5)
     */
    public function benchQueryCaching(): void
    {
        $json = Json::parse([
            'users' => array_map(fn ($i) => ['id' => $i, 'name' => 'user' . $i], range(1, 100)),
        ]);

        $cache = new MemoryStore();

        for ($i = 0; $i < 10; $i++) {
            $json->queryWithCache('$.users[?(@.id==1)]', $cache);
        }
    }

    /**
     * @Revs(10)
     * @Iterations(3)
     */
    public function benchStreamingBuilderConfiguration(): void
    {
        $builder = Json::streaming()
            ->withChunkSize(8192)
            ->withBufferLimit(10485760)
            ->withCache(new MemoryStore(), ttl: 3600)
            ->lazy()
            ->ndJson();

        $parser = $builder->buildParser();
    }
}
