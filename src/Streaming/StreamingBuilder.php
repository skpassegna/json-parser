<?php

declare(strict_types=1);

namespace Skpassegna\Json\Streaming;

use Psr\Http\Message\StreamInterface;
use Skpassegna\Json\Cache\MemoryStore;
use Skpassegna\Json\Contracts\CacheStoreInterface;

final class StreamingBuilder
{
    /**
     * @var int
     */
    private int $chunkSize = 8192;

    /**
     * @var int
     */
    private int $bufferLimit = 10485760;

    /**
     * @var ?CacheStoreInterface
     */
    private ?CacheStoreInterface $cache = null;

    /**
     * @var callable|null
     */
    private $onChunk = null;

    /**
     * @var callable|null
     */
    private $onError = null;

    /**
     * @var bool
     */
    private bool $enableCaching = false;

    /**
     * @var int|null
     */
    private ?int $cacheTtl = null;

    /**
     * @var bool
     */
    private bool $lazyMode = false;

    /**
     * @var bool
     */
    private bool $ndJsonMode = false;

    /**
     * Set chunk size in bytes.
     *
     * @param int $size Chunk size (default: 8192)
     * @return self
     */
    public function withChunkSize(int $size): self
    {
        if ($size < 1024) {
            $size = 1024;
        }
        if ($size > 1048576) {
            $size = 1048576;
        }
        
        $this->chunkSize = $size;
        return $this;
    }

    /**
     * Set buffer limit in bytes.
     *
     * @param int $bytes Buffer limit (default: 10MB)
     * @return self
     */
    public function withBufferLimit(int $bytes): self
    {
        $this->bufferLimit = $bytes;
        return $this;
    }

    /**
     * Enable caching with optional custom store.
     *
     * @param ?CacheStoreInterface $store Custom cache store (default: MemoryStore)
     * @param ?int $ttl Time to live in seconds (optional)
     * @return self
     */
    public function withCache(?CacheStoreInterface $store = null, ?int $ttl = null): self
    {
        $this->enableCaching = true;
        $this->cache = $store ?? new MemoryStore();
        $this->cacheTtl = $ttl;
        return $this;
    }

    /**
     * Disable caching.
     *
     * @return self
     */
    public function withoutCache(): self
    {
        $this->enableCaching = false;
        $this->cache = null;
        return $this;
    }

    /**
     * Set chunk event callback.
     *
     * @param callable $callback Callback(chunk, bytesProcessed)
     * @return self
     */
    public function onChunk(callable $callback): self
    {
        $this->onChunk = $callback;
        return $this;
    }

    /**
     * Set error event callback.
     *
     * @param callable $callback Callback(exception)
     * @return self
     */
    public function onError(callable $callback): self
    {
        $this->onError = $callback;
        return $this;
    }

    /**
     * Enable lazy-loading mode (defers parsing until access).
     *
     * @return self
     */
    public function lazy(): self
    {
        $this->lazyMode = true;
        return $this;
    }

    /**
     * Enable newline-delimited JSON mode.
     *
     * @return self
     */
    public function ndJson(): self
    {
        $this->ndJsonMode = true;
        return $this;
    }

    /**
     * Build a streaming parser instance.
     *
     * @return StreamingJsonParser
     */
    public function buildParser(): StreamingJsonParser
    {
        $parser = new StreamingJsonParser();

        if ($this->onChunk !== null) {
            $parser->onChunk($this->onChunk);
        }

        if ($this->onError !== null) {
            $parser->onError($this->onError);
        }

        return $parser;
    }

    /**
     * Build a streaming serializer instance.
     *
     * @return StreamingJsonSerializer
     */
    public function buildSerializer(): StreamingJsonSerializer
    {
        $serializer = new StreamingJsonSerializer();

        if ($this->onChunk !== null) {
            $serializer->onChunk($this->onChunk);
        }

        if ($this->onError !== null) {
            $serializer->onError($this->onError);
        }

        return $serializer;
    }

    /**
     * Parse a PSR-7 stream with builder configuration.
     *
     * @param StreamInterface $stream
     * @return \Generator<int, mixed, null, void>
     */
    public function parse(StreamInterface $stream): \Generator
    {
        $parser = $this->buildParser();

        if ($this->ndJsonMode) {
            yield from $parser->parseNdJson($stream, $this->chunkSize);
        } else {
            yield from $parser->parse($stream, $this->chunkSize);
        }
    }

    /**
     * Serialize data with builder configuration.
     *
     * @param mixed $data
     * @param StreamInterface $stream
     * @return \Generator<int, string, null, void>
     */
    public function serialize(mixed $data, StreamInterface $stream): \Generator
    {
        $serializer = $this->buildSerializer();

        if (is_array($data) && $this->ndJsonMode) {
            yield from $serializer->serializeNdJson($data, $stream);
        } else {
            yield from $serializer->serialize($data, $stream, $this->chunkSize);
        }
    }

    /**
     * Create a lazy proxy for deferred parsing.
     *
     * @param callable $loader
     * @param bool $prefetch
     * @return LazyJsonProxy
     */
    public function createProxy(callable $loader, bool $prefetch = false): LazyJsonProxy
    {
        return new LazyJsonProxy($loader, $prefetch);
    }

    /**
     * Get configured cache store (if enabled).
     *
     * @return ?CacheStoreInterface
     */
    public function getCache(): ?CacheStoreInterface
    {
        return $this->enableCaching ? $this->cache : null;
    }

    /**
     * Get cache TTL.
     *
     * @return ?int
     */
    public function getCacheTtl(): ?int
    {
        return $this->cacheTtl;
    }

    /**
     * Get chunk size.
     *
     * @return int
     */
    public function getChunkSize(): int
    {
        return $this->chunkSize;
    }

    /**
     * Get buffer limit.
     *
     * @return int
     */
    public function getBufferLimit(): int
    {
        return $this->bufferLimit;
    }

    /**
     * Check if lazy mode is enabled.
     *
     * @return bool
     */
    public function isLazy(): bool
    {
        return $this->lazyMode;
    }

    /**
     * Check if NDJSON mode is enabled.
     *
     * @return bool
     */
    public function isNdJson(): bool
    {
        return $this->ndJsonMode;
    }
}
