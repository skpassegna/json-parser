<?php

declare(strict_types=1);

namespace Skpassegna\Json\Streaming;

use Psr\Http\Message\StreamInterface;
use Skpassegna\Json\Contracts\StreamingSerializerInterface;
use Skpassegna\Json\Exceptions\IOException;
use Skpassegna\Json\Exceptions\RuntimeException;

final class StreamingJsonSerializer implements StreamingSerializerInterface
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
    public function serialize(mixed $data, StreamInterface $stream, int $chunkSize = 8192): \Generator
    {
        try {
            $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
            
            if ($json === false) {
                throw new RuntimeException('Failed to encode data to JSON');
            }

            $bytesWritten = 0;
            for ($i = 0; $i < strlen($json); $i += $chunkSize) {
                $chunk = substr($json, $i, $chunkSize);
                $stream->write($chunk);
                $bytesWritten += strlen($chunk);
                $this->invokeOnChunk($chunk, $bytesWritten);
                yield $chunk;
            }
        } catch (\JsonException $e) {
            $this->invokeOnError($e);
            throw new RuntimeException(sprintf('JSON encoding error: %s', $e->getMessage()), 0, $e);
        } catch (\Throwable $e) {
            $this->invokeOnError($e);
            throw new IOException(sprintf('Stream writing error: %s', $e->getMessage()), 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function serializeNdJson(array $items, StreamInterface $stream): \Generator
    {
        $bytesWritten = 0;

        foreach ($items as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
                
                if ($json === false) {
                    throw new RuntimeException('Failed to encode item to JSON');
                }

                $line = $json . "\n";
                $stream->write($line);
                $bytesWritten += strlen($line);
                $this->invokeOnChunk($line, $bytesWritten);
                yield $line;
            } catch (\JsonException $e) {
                $this->invokeOnError($e);
                throw new RuntimeException(sprintf('JSON encoding error: %s', $e->getMessage()), 0, $e);
            } catch (\Throwable $e) {
                $this->invokeOnError($e);
                throw new IOException(sprintf('Stream writing error: %s', $e->getMessage()), 0, $e);
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
     * Invoke the chunk callback if registered.
     */
    private function invokeOnChunk(string $chunk, int $bytesWritten): void
    {
        if ($this->onChunkCallback !== null) {
            ($this->onChunkCallback)($chunk, $bytesWritten);
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
