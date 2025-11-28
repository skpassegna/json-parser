<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests\Streaming;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Skpassegna\Json\Streaming\StreamingJsonSerializer;

class StreamingJsonSerializerTest extends TestCase
{
    private function createStream(): StreamInterface
    {
        $content = '';
        $stream = $this->createMock(StreamInterface::class);
        
        $stream->method('write')->willReturnCallback(function ($data) use (&$content) {
            $content .= $data;
            return strlen($data);
        });
        
        $stream->method('__toString')->willReturnCallback(function () use (&$content) {
            return $content;
        });
        
        return $stream;
    }

    public function testSerializeObject(): void
    {
        $data = ['name' => 'John', 'age' => 30];
        $stream = $this->createStream();
        
        $serializer = new StreamingJsonSerializer();
        $chunks = [];
        
        foreach ($serializer->serialize($data, $stream) as $chunk) {
            $chunks[] = $chunk;
        }
        
        $this->assertGreaterThan(0, count($chunks));
        $result = implode('', $chunks);
        $this->assertStringContainsString('name', $result);
        $this->assertStringContainsString('John', $result);
    }

    public function testSerializeArray(): void
    {
        $data = [1, 2, 3, 4, 5];
        $stream = $this->createStream();
        
        $serializer = new StreamingJsonSerializer();
        $chunks = [];
        
        foreach ($serializer->serialize($data, $stream) as $chunk) {
            $chunks[] = $chunk;
        }
        
        $result = implode('', $chunks);
        $decoded = json_decode($result, true);
        
        $this->assertEquals([1, 2, 3, 4, 5], $decoded);
    }

    public function testSerializeNdJson(): void
    {
        $items = [
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob'],
            ['id' => 3, 'name' => 'Charlie'],
        ];
        $stream = $this->createStream();
        
        $serializer = new StreamingJsonSerializer();
        $lines = [];
        
        foreach ($serializer->serializeNdJson($items, $stream) as $line) {
            $lines[] = $line;
        }
        
        $this->assertCount(3, $lines);
        
        foreach ($lines as $line) {
            $this->assertStringEndsWith("\n", $line);
        }
    }

    public function testChunkCallback(): void
    {
        $data = ['test' => 'data'];
        $stream = $this->createStream();
        
        $chunkCount = 0;
        $serializer = new StreamingJsonSerializer();
        $serializer->onChunk(function ($chunk, $bytes) use (&$chunkCount) {
            $chunkCount++;
        });
        
        foreach ($serializer->serialize($data, $stream) as $chunk) {
            // iterate
        }
        
        $this->assertGreaterThan(0, $chunkCount);
    }

    public function testSerializeNestedStructure(): void
    {
        $data = [
            'users' => [
                ['id' => 1, 'name' => 'Alice'],
                ['id' => 2, 'name' => 'Bob'],
            ],
            'total' => 2,
        ];
        $stream = $this->createStream();
        
        $serializer = new StreamingJsonSerializer();
        $chunks = [];
        
        foreach ($serializer->serialize($data, $stream) as $chunk) {
            $chunks[] = $chunk;
        }
        
        $result = implode('', $chunks);
        $decoded = json_decode($result, true);
        
        $this->assertCount(2, $decoded['users']);
        $this->assertEquals(2, $decoded['total']);
    }
}
