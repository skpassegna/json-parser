<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests\Events;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Events\JsonEvent;
use Skpassegna\Json\Enums\EventType;

class JsonEventTest extends TestCase
{
    public function testCreateBeforeEvent(): void
    {
        $event = JsonEvent::beforeOperation(EventType::BEFORE_PARSE, 'parse', ['test' => 'data']);

        $this->assertEquals(EventType::BEFORE_PARSE->value, $event->getEventType());
        $this->assertEquals('parse', $event->getOperation());
        $this->assertEquals(['test' => 'data'], $event->getData());
        $this->assertNull($event->getError());
    }

    public function testCreateAfterEvent(): void
    {
        $data = ['test' => 'data'];
        $result = ['processed' => true];

        $event = JsonEvent::afterOperation(EventType::AFTER_PARSE, 'parse', $data, $result);

        $this->assertEquals(EventType::AFTER_PARSE->value, $event->getEventType());
        $this->assertEquals('parse', $event->getOperation());
        $this->assertEquals($data, $event->getData());
        $this->assertEquals($result, $event->getResult());
    }

    public function testCreateErrorEvent(): void
    {
        $error = new \Exception('Test error');

        $event = JsonEvent::onError(EventType::PARSE_ERROR, 'parse', [], $error, ['line' => 1]);

        $this->assertEquals(EventType::PARSE_ERROR->value, $event->getEventType());
        $this->assertEquals('parse', $event->getOperation());
        $this->assertSame($error, $event->getError());
        $this->assertEquals(['line' => 1], $event->getContext());
    }

    public function testSetAndGetMetadata(): void
    {
        $event = JsonEvent::beforeOperation(EventType::BEFORE_PARSE, 'parse', []);

        $event->setMetadata('key1', 'value1');
        $event->setMetadata('key2', 42);

        $metadata = $event->getMetadata();

        $this->assertEquals('value1', $metadata['key1']);
        $this->assertEquals(42, $metadata['key2']);
    }

    public function testSetAndGetResult(): void
    {
        $event = JsonEvent::beforeOperation(EventType::BEFORE_PARSE, 'parse', []);

        $this->assertNull($event->getResult());

        $result = ['parsed' => 'data'];
        $event->setResult($result);

        $this->assertEquals($result, $event->getResult());
    }

    public function testPropagationControl(): void
    {
        $event = JsonEvent::beforeOperation(EventType::BEFORE_PARSE, 'parse', []);

        $this->assertFalse($event->isPropagationStopped());

        $event->stopPropagation();

        $this->assertTrue($event->isPropagationStopped());
    }

    public function testGetTimestamp(): void
    {
        $before = time() + (microtime(true) - floor(microtime(true)));
        $event = JsonEvent::beforeOperation(EventType::BEFORE_PARSE, 'parse', []);
        $after = time() + (microtime(true) - floor(microtime(true)));

        $timestamp = $event->getTimestamp();

        $this->assertGreaterThanOrEqual($before, $timestamp);
        $this->assertLessThanOrEqual($after, $timestamp);
    }

    public function testToArray(): void
    {
        $error = new \Exception('Test error');
        $event = JsonEvent::onError(EventType::PARSE_ERROR, 'parse', ['data'], $error, ['context' => 'value']);
        $event->setMetadata('processed', true);

        $array = $event->toArray();

        $this->assertEquals(EventType::PARSE_ERROR->value, $array['event_type']);
        $this->assertEquals('parse', $array['operation']);
        $this->assertTrue($array['has_error']);
        $this->assertEquals('Test error', $array['error']['message']);
        $this->assertEquals('processed', array_keys($array['metadata'])[0] ?? null);
    }

    public function testEventTypeEnum(): void
    {
        $this->assertTrue(EventType::BEFORE_PARSE->isBeforeEvent());
        $this->assertTrue(EventType::AFTER_PARSE->isAfterEvent());
        $this->assertTrue(EventType::PARSE_ERROR->isErrorEvent());
        $this->assertEquals('parse', EventType::BEFORE_PARSE->getOperation());
    }
}
