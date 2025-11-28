<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests\Events;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Json;
use Skpassegna\Json\Events\EventDispatcher;
use Skpassegna\Json\Enums\EventType;
use Skpassegna\Json\Enums\DiffMergeStrategy;

class IntegrationTest extends TestCase
{
    public function testJsonFacadeEventDispatching(): void
    {
        $json = Json::parse(['name' => 'John']);
        $events = [];

        $dispatcher = new EventDispatcher();
        $dispatcher->subscribe(EventType::BEFORE_MERGE->value, function ($event) use (&$events) {
            $events[] = 'before_merge';
        });
        $dispatcher->subscribe(EventType::AFTER_MERGE->value, function ($event) use (&$events) {
            $events[] = 'after_merge';
        });

        $json->setInstanceDispatcher($dispatcher);
        $json->mergeWithStrategy(['age' => 30], DiffMergeStrategy::MERGE_RECURSIVE);

        $this->assertContains('before_merge', $events);
        $this->assertContains('after_merge', $events);
    }

    public function testReflectionInspectorWithDispatcher(): void
    {
        $data = ['users' => [['name' => 'John']]];
        $dispatcher = new EventDispatcher();
        $dispatcher->subscribe('test.event', function () {});
        $dispatcher->subscribe('test.event', function () {});

        $json = Json::parse($data);
        $json->setInstanceDispatcher($dispatcher);

        $inspector = $json->reflect(true);
        $info = $inspector->getListenerInfo();

        $this->assertEquals(2, count($info['test.event']));
    }

    public function testDiffWithStrategy(): void
    {
        $source = Json::parse(['a' => 1, 'b' => 2]);
        $target = ['a' => 1, 'c' => 3];

        $diff = $source->diffWithStrategy($target, DiffMergeStrategy::DIFF_SUMMARY);

        $this->assertEquals(1, $diff['added_count']);
        $this->assertEquals(1, $diff['removed_count']);
    }

    public function testMergeRfc7396Strategy(): void
    {
        $json = Json::parse(['name' => 'John', 'age' => 30, 'city' => 'NYC']);
        $json->mergeWithStrategy(
            ['age' => null, 'country' => 'USA'],
            DiffMergeStrategy::MERGE_PATCH_RFC7396
        );

        $data = $json->getData();

        $this->assertEquals('John', $data['name']);
        $this->assertArrayNotHasKey('age', $data);
        $this->assertEquals('USA', $data['country']);
    }

    public function testConflictAwareMerge(): void
    {
        $base = ['a' => 1, 'b' => 2];
        $target = ['a' => 1, 'b' => 3];
        $source = ['a' => 1, 'b' => 4];

        $result = (new \ReflectionClass('Skpassegna\Json\Utils\DiffMergeStrategies'))
            ->getMethod('mergeConflictAware')
            ->invoke(null, $target, $source, $base);

        $this->assertEquals(4, $result['result']['b']);
    }

    public function testGlobalDispatcher(): void
    {
        $dispatcher = Json::dispatcher();
        $dispatcher->subscribe('global.event', function () {});

        $types = $dispatcher->getEventTypes();

        $this->assertContains('global.event', $types);

        $dispatcher->clearListeners();
    }

    public function testEventListenerSubscription(): void
    {
        $events = [];

        $json = Json::parse(['test' => 'data']);
        $dispatcher = $json->getDispatcher();

        $dispatcher->subscribe('custom.event', function () use (&$events) {
            $events[] = 'triggered';
        });

        $this->assertTrue($dispatcher->hasListeners('custom.event'));
        $this->assertEquals(1, count($dispatcher->getListeners('custom.event')));
    }

    public function testReflectionDump(): void
    {
        $json = Json::parse(['name' => 'John', 'age' => 30]);

        $inspector = $json->reflect(false);
        $dump = $inspector->dump();

        $this->assertStringContainsString('JSON Document Reflection', $dump);
        $this->assertStringContainsString('array', strtolower($dump));
    }

    public function testRfc6902PatchCompliance(): void
    {
        $source = ['a' => 1, 'b' => ['c' => 2]];
        $target = ['a' => 1, 'b' => ['c' => 3, 'd' => 4]];

        $json = Json::parse($source);
        $patches = $json->diffWithStrategy($target, DiffMergeStrategy::DIFF_RFC6902_PATCH);

        foreach ($patches as $patch) {
            $this->assertArrayHasKey('op', $patch);
            $this->assertArrayHasKey('path', $patch);
            $this->assertContains($patch['op'], ['add', 'remove', 'replace']);
            $this->assertStringStartsWith('/', $patch['path']);
        }
    }

    public function testEventContextAndMetadata(): void
    {
        $captured = null;

        $json = Json::parse(['data' => 'value']);
        $dispatcher = $json->getDispatcher();

        $dispatcher->subscribe(EventType::BEFORE_MERGE->value, function ($event) use (&$captured) {
            $captured = $event;
        });

        $json->mergeWithStrategy(['new' => 'data'], DiffMergeStrategy::MERGE_RECURSIVE);

        $this->assertNotNull($captured);
        $this->assertArrayHasKey('strategy', $captured->getContext());
        $this->assertEquals(DiffMergeStrategy::MERGE_RECURSIVE->value, $captured->getContext()['strategy']);
    }
}
