<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests\Utils;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Utils\ReflectionInspector;
use Skpassegna\Json\Events\EventDispatcher;

class ReflectionInspectorTest extends TestCase
{
    public function testDescribeSimpleArray(): void
    {
        $data = ['name' => 'John', 'age' => 30];
        $inspector = new ReflectionInspector($data);

        $description = $inspector->describeDocument();

        $this->assertEquals('array', $description['type']);
        $this->assertEquals(2, $description['properties_count']);
        $this->assertTrue($description['is_associative']);
    }

    public function testDescribeNestedStructure(): void
    {
        $data = [
            'user' => [
                'name' => 'John',
                'addresses' => [
                    ['city' => 'New York'],
                    ['city' => 'Boston'],
                ],
            ],
        ];
        $inspector = new ReflectionInspector($data);

        $description = $inspector->describeDocument();

        $this->assertGreaterThan(1, $description['depth']);
        $this->assertGreaterThan(2, $description['properties_count']);
    }

    public function testInferSchema(): void
    {
        $data = [
            'name' => 'John',
            'age' => 30,
            'active' => true,
            'tags' => ['php', 'json'],
        ];
        $inspector = new ReflectionInspector($data);

        $schema = $inspector->inferSchema();

        $this->assertEquals('object', $schema['type']);
        $this->assertEquals('string', $schema['properties']['name']['type']);
        $this->assertEquals('integer', $schema['properties']['age']['type']);
        $this->assertEquals('boolean', $schema['properties']['active']['type']);
        $this->assertEquals('array', $schema['properties']['tags']['type']);
    }

    public function testGetNodeMetadata(): void
    {
        $data = [
            'user' => [
                'name' => 'John',
                'age' => 30,
            ],
        ];
        $inspector = new ReflectionInspector($data);

        $metadata = $inspector->getNodeMetadata('/user/name');

        $this->assertTrue($metadata['exists']);
        $this->assertEquals('string', $metadata['type']);
    }

    public function testGetNodeMetadataForNonexistentPath(): void
    {
        $data = ['name' => 'John'];
        $inspector = new ReflectionInspector($data);

        $metadata = $inspector->getNodeMetadata('/nonexistent');

        $this->assertFalse($metadata['exists']);
        $this->assertArrayHasKey('error', $metadata);
    }

    public function testGetStructureStats(): void
    {
        $data = [
            'users' => [
                ['name' => 'John', 'age' => 30],
                ['name' => 'Jane', 'age' => 25],
            ],
            'count' => 2,
            'active' => true,
        ];
        $inspector = new ReflectionInspector($data);

        $stats = $inspector->getStructureStats();

        $this->assertArrayHasKey('total_keys', $stats);
        $this->assertArrayHasKey('total_nodes', $stats);
        $this->assertArrayHasKey('depth', $stats);
        $this->assertArrayHasKey('leaf_count', $stats);
        $this->assertGreaterThan(0, $stats['total_nodes']);
    }

    public function testGetListenerInfo(): void
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->subscribe('event1', function () {});
        $dispatcher->subscribe('event1', function () {});
        $dispatcher->subscribe('event2', function () {});

        $data = [];
        $inspector = new ReflectionInspector($data, $dispatcher);

        $listeners = $inspector->getListenerInfo();

        $this->assertEquals(2, count($listeners['event1']));
        $this->assertEquals(1, count($listeners['event2']));
    }

    public function testDump(): void
    {
        $data = ['name' => 'John', 'age' => 30];
        $inspector = new ReflectionInspector($data);

        $dump = $inspector->dump();

        $this->assertStringContainsString('JSON Document Reflection', $dump);
        $this->assertStringContainsString('Document Description', $dump);
        $this->assertStringContainsString('Structure Statistics', $dump);
        $this->assertStringContainsString('Inferred Schema', $dump);
    }

    public function testToArray(): void
    {
        $data = ['name' => 'John', 'age' => 30];
        $inspector = new ReflectionInspector($data);

        $array = $inspector->toArray();

        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('schema', $array);
        $this->assertArrayHasKey('statistics', $array);
        $this->assertArrayHasKey('listeners', $array);
    }

    public function testCachingNodeMetadata(): void
    {
        $data = ['test' => 'value'];
        $inspector = new ReflectionInspector($data);

        $metadata1 = $inspector->getNodeMetadata('/test');
        $metadata2 = $inspector->getNodeMetadata('/test');

        $this->assertEquals($metadata1, $metadata2);
    }

    public function testDetectsNullValues(): void
    {
        $data = ['name' => 'John', 'optional' => null];
        $inspector = new ReflectionInspector($data);

        $stats = $inspector->getStructureStats();

        $this->assertEquals(1, $stats['null_count']);
    }
}
