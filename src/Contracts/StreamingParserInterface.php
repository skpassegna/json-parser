<?php

declare(strict_types=1);

namespace Skpassegna\Json\Contracts;

use Psr\Http\Message\StreamInterface;

interface StreamingParserInterface
{
    /**
     * Parse a PSR-7 stream and yield decoded JSON chunks.
     *
     * @param StreamInterface $stream The PSR-7 stream to parse
     * @param int $chunkSize Size of each chunk to read (in bytes)
     * @return \Generator<int, mixed, null, void> Yields parsed JSON values
     */
    public function parse(StreamInterface $stream, int $chunkSize = 8192): \Generator;

    /**
     * Parse newline-delimited JSON (NDJSON) from stream.
     *
     * @param StreamInterface $stream The PSR-7 stream to parse
     * @param int $chunkSize Size of each chunk to read (in bytes)
     * @param callable|null $onError Error callback
     * @return \Generator<int, mixed, null, void> Yields parsed JSON objects
     */
    public function parseNdJson(StreamInterface $stream, int $chunkSize = 8192, ?callable $onError = null): \Generator;

    /**
     * Set a callback for chunk processing events.
     *
     * @param callable $callback Callback(chunkData, bytesRead)
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
