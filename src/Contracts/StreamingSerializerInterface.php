<?php

declare(strict_types=1);

namespace Skpassegna\Json\Contracts;

use Psr\Http\Message\StreamInterface;

interface StreamingSerializerInterface
{
    /**
     * Serialize data to a PSR-7 stream as chunks using generators.
     *
     * @param mixed $data The data to serialize
     * @param StreamInterface $stream Target PSR-7 stream
     * @param int $chunkSize Size of each chunk to write (in bytes)
     * @return \Generator<int, string, null, void> Yields JSON string chunks
     */
    public function serialize(mixed $data, StreamInterface $stream, int $chunkSize = 8192): \Generator;

    /**
     * Serialize data as newline-delimited JSON (NDJSON) to stream.
     *
     * @param array $items Array of items to serialize as NDJSON
     * @param StreamInterface $stream Target PSR-7 stream
     * @return \Generator<int, string, null, void> Yields NDJSON lines
     */
    public function serializeNdJson(array $items, StreamInterface $stream): \Generator;

    /**
     * Set a callback for chunk writing events.
     *
     * @param callable $callback Callback(chunk, bytesWritten)
     * @return self
     */
    public function onChunk(callable $callback): self;

    /**
     * Set a callback for error events.
     *
     * @param callable $callback Callback(exception)
     * @return self
     */
    public function onError(callable $callback): self;
}
