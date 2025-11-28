<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests\Streaming;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Skpassegna\Json\Streaming\StreamingJsonParser;

class StreamingJsonParserTest extends TestCase
{
    private function createStream(string $content): StreamInterface
    {
        $stream = $this->createMock(StreamInterface::class);
        $chunks = str_split($content, 100);
        
        $stream->method('eof')->willReturnOnConsecutiveCalls(
            ...array_pad(array_fill(0, count($chunks), false), count($chunks) + 1, true)
        );
        
        $stream->method('read')->willReturnOnConsecutiveCalls(...$chunks);
        $stream->method('rewind');
        
        return $stream;
    }

    public function testParseJsonObject(): void
    {
        $json = '{"name":"John","age":30}';
        $stream = $this->createStream($json);
        
        $parser = new StreamingJsonParser();
        $results = [];
        
        foreach ($parser->parse($stream) as $item) {
            $results[] = $item;
        }
        
        $this->assertCount(1, $results);
        $this->assertEquals(['name' => 'John', 'age' => 30], $results[0]);
    }

    public function testParseJsonArray(): void
    {
        $json = '[1,2,3,4,5]';
        $stream = $this->createStream($json);
        
        $parser = new StreamingJsonParser();
        $results = [];
        
        foreach ($parser->parse($stream) as $item) {
            $results[] = $item;
        }
        
        $this->assertCount(1, $results);
        $this->assertEquals([1, 2, 3, 4, 5], $results[0]);
    }

    public function testParseMultipleObjects(): void
    {
        $json = '{"a":1}{"b":2}{"c":3}';
        $stream = $this->createStream($json);
        
        $parser = new StreamingJsonParser();
        $results = [];
        
        foreach ($parser->parse($stream) as $item) {
            $results[] = $item;
        }
        
        $this->assertCount(3, $results);
        $this->assertEquals(['a' => 1], $results[0]);
        $this->assertEquals(['b' => 2], $results[1]);
        $this->assertEquals(['c' => 3], $results[2]);
    }

    public function testParseNdJson(): void
    {
        $ndjson = "{\"id\":1}\n{\"id\":2}\n{\"id\":3}\n";
        $stream = $this->createStream($ndjson);
        
        $parser = new StreamingJsonParser();
        $results = [];
        
        foreach ($parser->parseNdJson($stream) as $item) {
            $results[] = $item;
        }
        
        $this->assertCount(3, $results);
        $this->assertEquals(['id' => 1], $results[0]);
        $this->assertEquals(['id' => 2], $results[1]);
        $this->assertEquals(['id' => 3], $results[2]);
    }

    public function testChunkCallback(): void
    {
        $json = '{"test":true}';
        $stream = $this->createStream($json);
        
        $chunkCount = 0;
        $parser = new StreamingJsonParser();
        $parser->onChunk(function ($chunk, $bytes) use (&$chunkCount) {
            $chunkCount++;
        });
        
        foreach ($parser->parse($stream) as $item) {
            // iterate
        }
        
        $this->assertGreaterThan(0, $chunkCount);
    }

    public function testEmptyStream(): void
    {
        $json = '';
        $stream = $this->createStream($json);
        
        $parser = new StreamingJsonParser();
        $results = [];
        
        foreach ($parser->parse($stream) as $item) {
            $results[] = $item;
        }
        
        $this->assertCount(0, $results);
    }
}
