<?php

declare(strict_types=1);

/**
 * Basic Streaming Example
 * 
 * Demonstrates parsing large JSON files from a stream without loading
 * the entire file into memory at once.
 */

use Psr\Http\Message\StreamInterface;
use Skpassegna\Json\Json;

require __DIR__ . '/../../vendor/autoload.php';

// Example: Parse streaming JSON from an array source
// (In real scenarios, you'd use a PSR-7 stream implementation like Guzzle)

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

    // Other StreamInterface methods (stub implementations)
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

// Example 1: Stream large JSON objects
echo "=== Example 1: Streaming JSON Objects ===\n";
$largeJson = '{"users":[{"id":1,"name":"Alice"},{"id":2,"name":"Bob"}]}{"result":"success"}';
$stream = new SimpleStream($largeJson);

foreach (Json::parseStream($stream, chunkSize: 50) as $item) {
    echo json_encode($item) . "\n";
}

// Example 2: Stream newline-delimited JSON (NDJSON)
echo "\n=== Example 2: Streaming NDJSON ===\n";
$ndjson = <<<NDJSON
{"id":1,"name":"Alice","email":"alice@example.com"}
{"id":2,"name":"Bob","email":"bob@example.com"}
{"id":3,"name":"Charlie","email":"charlie@example.com"}
NDJSON;

$stream = new SimpleStream($ndjson);

foreach (Json::parseNdJsonStream($stream, chunkSize: 100) as $user) {
    printf("User: %s (%s)\n", $user['name'], $user['email']);
}

// Example 3: Using the builder for advanced configuration
echo "\n=== Example 3: Builder with Callbacks ===\n";
$json = '{"data":[1,2,3,4,5]}';
$stream = new SimpleStream($json);

$builder = Json::streaming()
    ->withChunkSize(30)
    ->onChunk(function ($chunk, $bytes) {
        echo "  - Read $bytes bytes\n";
    });

foreach ($builder->parse($stream) as $data) {
    echo "Parsed: " . json_encode($data) . "\n";
}

echo "\n✓ Streaming examples completed!\n";
