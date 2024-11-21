<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Contracts\SerializerInterface;
use Skpassegna\Json\Exceptions\RuntimeException;

class SerializerTest extends TestCase
{
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    public function testSerializeSuccess(): void
    {
        $data = ['name' => 'John', 'age' => 30];
        $expected = '{"name":"John","age":30}';

        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with($data)
            ->willReturn($expected);

        $result = $this->serializer->serialize($data);
        $this->assertEquals($expected, $result);
    }

    public function testSerializeFailure(): void
    {
        $data = ['recursive' => &$data];

        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with($data)
            ->willThrowException(new RuntimeException('Cannot encode recursive references'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot encode recursive references');

        $this->serializer->serialize($data);
    }

    public function testDeserializeSuccess(): void
    {
        $json = '{"name":"John","age":30}';
        $expected = ['name' => 'John', 'age' => 30];

        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($json, true)
            ->willReturn($expected);

        $result = $this->serializer->deserialize($json, true);
        $this->assertEquals($expected, $result);
    }

    public function testDeserializeFailure(): void
    {
        $json = '{"invalid": json}';

        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($json)
            ->willThrowException(new RuntimeException('Syntax error'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Syntax error');

        $this->serializer->deserialize($json);
    }
}
