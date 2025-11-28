<?php

declare(strict_types=1);

namespace Skpassegna\Json\Streaming;

use Psr\Http\Message\StreamInterface;
use Skpassegna\Json\Contracts\StreamingParserInterface;
use Skpassegna\Json\Exceptions\IOException;
use Skpassegna\Json\Exceptions\ParseException;

final class StreamingJsonParser implements StreamingParserInterface
{
    /**
     * @var callable|null
     */
    private $onChunkCallback = null;

    /**
     * @var callable|null
     */
    private $onErrorCallback = null;

    /**
     * @inheritDoc
     */
    public function parse(StreamInterface $stream, int $chunkSize = 8192): \Generator
    {
        $stream->rewind();
        $buffer = '';
        $depth = 0;
        $inString = false;
        $escaped = false;
        $totalBytesRead = 0;

        while (!$stream->eof()) {
            try {
                $chunk = $stream->read($chunkSize);
                if ($chunk === '') {
                    break;
                }

                $totalBytesRead += strlen($chunk);
                $this->invokeOnChunk($chunk, $totalBytesRead);
                
                $buffer .= $chunk;
                
                $result = $this->extractCompleteObjects($buffer, $depth, $inString, $escaped);
                foreach ($result['objects'] as $obj) {
                    yield $obj;
                }

                $buffer = $result['remaining'];
                $depth = $result['depth'];
                $inString = $result['inString'];
                $escaped = $result['escaped'];
            } catch (\Throwable $e) {
                $this->invokeOnError($e);
                throw new IOException("Stream reading error: {$e->getMessage()}", 0, $e);
            }
        }

        if (trim($buffer) !== '') {
            try {
                $decoded = json_decode($buffer, true, 512, JSON_THROW_ON_ERROR);
                yield $decoded;
            } catch (\JsonException $e) {
                $this->invokeOnError($e);
                throw new ParseException("Failed to parse remaining buffer: {$e->getMessage()}", 0, $e);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function parseNdJson(StreamInterface $stream, int $chunkSize = 8192, ?callable $onError = null): \Generator
    {
        $stream->rewind();
        $buffer = '';
        $totalBytesRead = 0;

        while (!$stream->eof()) {
            try {
                $chunk = $stream->read($chunkSize);
                if ($chunk === '') {
                    break;
                }

                $totalBytesRead += strlen($chunk);
                $this->invokeOnChunk($chunk, $totalBytesRead);
                
                $buffer .= $chunk;
                
                $lines = explode("\n", $buffer);
                $buffer = array_pop($lines);

                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '') {
                        continue;
                    }

                    try {
                        $decoded = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
                        yield $decoded;
                    } catch (\JsonException $e) {
                        $errorMsg = "Failed to parse NDJSON line: {$e->getMessage()}";
                        if ($onError !== null) {
                            $onError(new ParseException($errorMsg, 0, $e));
                        } else {
                            $this->invokeOnError($e);
                        }
                    }
                }
            } catch (\Throwable $e) {
                $this->invokeOnError($e);
                throw new IOException("Stream reading error: {$e->getMessage()}", 0, $e);
            }
        }

        if (trim($buffer) !== '') {
            try {
                $decoded = json_decode($buffer, true, 512, JSON_THROW_ON_ERROR);
                yield $decoded;
            } catch (\JsonException $e) {
                $errorMsg = "Failed to parse remaining NDJSON buffer: {$e->getMessage()}";
                if ($onError !== null) {
                    $onError(new ParseException($errorMsg, 0, $e));
                } else {
                    $this->invokeOnError($e);
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function onChunk(callable $callback): self
    {
        $this->onChunkCallback = $callback;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function onError(callable $callback): self
    {
        $this->onErrorCallback = $callback;
        return $this;
    }

    /**
     * Extract complete JSON objects from buffer.
     *
     * @param string $buffer
     * @param int $depth
     * @param bool $inString
     * @param bool $escaped
     * @return array{objects: array<mixed>, remaining: string, depth: int, inString: bool, escaped: bool}
     */
    private function extractCompleteObjects(
        string $buffer,
        int $depth,
        bool $inString,
        bool $escaped
    ): array {
        $objects = [];
        $remaining = $buffer;
        $i = 0;
        $startIndex = 0;
        $depth = $depth;
        $inString = $inString;
        $escaped = $escaped;

        while ($i < strlen($remaining)) {
            $char = $remaining[$i];

            if ($escaped) {
                $escaped = false;
                $i++;
                continue;
            }

            if ($char === '\\') {
                $escaped = true;
                $i++;
                continue;
            }

            if ($char === '"' && !$escaped) {
                $inString = !$inString;
                $i++;
                continue;
            }

            if ($inString) {
                $i++;
                continue;
            }

            if ($char === '{' || $char === '[') {
                if ($depth === 0) {
                    $startIndex = $i;
                }
                $depth++;
            } elseif ($char === '}' || $char === ']') {
                $depth--;
                if ($depth === 0 && $startIndex < $i) {
                    $jsonStr = substr($remaining, $startIndex, $i - $startIndex + 1);
                    try {
                        $decoded = json_decode($jsonStr, true, 512, JSON_THROW_ON_ERROR);
                        $objects[] = $decoded;
                    } catch (\JsonException $e) {
                        // Malformed JSON, skip
                    }
                    $startIndex = $i + 1;
                }
            }

            $i++;
        }

        if ($startIndex < strlen($remaining)) {
            $remaining = substr($remaining, $startIndex);
        } else {
            $remaining = '';
        }

        return [
            'objects' => $objects,
            'remaining' => $remaining,
            'depth' => $depth,
            'inString' => $inString,
            'escaped' => $escaped,
        ];
    }

    /**
     * Invoke the chunk callback if registered.
     */
    private function invokeOnChunk(string $chunk, int $bytesRead): void
    {
        if ($this->onChunkCallback !== null) {
            ($this->onChunkCallback)($chunk, $bytesRead);
        }
    }

    /**
     * Invoke the error callback if registered.
     */
    private function invokeOnError(\Throwable $e): void
    {
        if ($this->onErrorCallback !== null) {
            ($this->onErrorCallback)($e);
        }
    }
}
