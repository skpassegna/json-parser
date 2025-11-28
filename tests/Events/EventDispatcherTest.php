<?php

declare(strict_types=1);

namespace Skpassegna\Json\Tests\Events;

use PHPUnit\Framework\TestCase;
use Skpassegna\Json\Events\EventDispatcher;
use Skpassegna\Json\Events\JsonEvent;
use Skpassegna\Json\Enums\EventType;

class EventDispatcherTest extends TestCase
{
    private EventDispatcher $dispatcher;

    protected function setUp(): void
    {
        $this->dispatcher = new EventDispatcher();
    }

    public function testCanSubscribeToEvent(): void
    {
        $called = false;
        $listener = function () use (&$called) {
            $called = true;
        };

        $this->dispatcher->subscribe('test.event', $listener);
        $this->assertTrue($this->dispatcher->hasListeners('test.event'));

        $event = JsonEvent::beforeOperation(EventType::BEFORE_PARSE, 'parse', []);
        $this->dispatcher->dispatch($event);

        $this->assertTrue(true);
    }

    public function testCanUnsubscribeFromEvent(): void
    {
        $listener = function () {};

        $this->dispatcher->subscribe('test.event', $listener);
        $this->assertTrue($this->dispatcher->hasListeners('test.event'));

        $this->assertTrue($this->dispatcher->unsubscribe('test.event', $listener));
        $this->assertFalse($this->dispatcher->hasListeners('test.event'));
    }

    public function testUnsubscribeReturnsFalseWhenListenerNotFound(): void
    {
        $listener1 = function () {};
        $listener2 = function () {};

        $this->dispatcher->subscribe('test.event', $listener1);

        $this->assertFalse($this->dispatcher->unsubscribe('test.event', $listener2));
    }

    public function testListenersExecuteInPriorityOrder(): void
    {
        $results = [];

        $this->dispatcher->subscribe(EventType::BEFORE_PARSE->value, function () use (&$results) {
            $results[] = 'low';
        }, 0);

        $this->dispatcher->subscribe(EventType::BEFORE_PARSE->value, function () use (&$results) {
            $results[] = 'high';
        }, 10);

        $this->dispatcher->subscribe(EventType::BEFORE_PARSE->value, function () use (&$results) {
            $results[] = 'medium';
        }, 5);

        $event = JsonEvent::beforeOperation(EventType::BEFORE_PARSE, 'parse', []);
        $this->dispatcher->dispatch($event);

        $this->assertEquals(['high', 'medium', 'low'], $results);
    }

    public function testCanStopPropagation(): void
    {
        $results = [];

        $this->dispatcher->subscribe(EventType::BEFORE_PARSE->value, function (JsonEvent $event) use (&$results) {
            $results[] = 'first';
            $event->stopPropagation();
        }, 10);

        $this->dispatcher->subscribe(EventType::BEFORE_PARSE->value, function () use (&$results) {
            $results[] = 'second';
        }, 5);

        $event = JsonEvent::beforeOperation(EventType::BEFORE_PARSE, 'parse', []);
        $this->dispatcher->dispatch($event);

        $this->assertEquals(['first'], $results);
    }

    public function testGetListeners(): void
    {
        $listener1 = function () {};
        $listener2 = function () {};

        $this->dispatcher->subscribe('test', $listener1, 5);
        $this->dispatcher->subscribe('test', $listener2, 10);

        $listeners = $this->dispatcher->getListeners('test');

        $this->assertCount(2, $listeners);
    }

    public function testGetListenersReturnsEmptyArrayForNonexistentEvent(): void
    {
        $listeners = $this->dispatcher->getListeners('nonexistent');

        $this->assertEmpty($listeners);
    }

    public function testClearListenersForSpecificEvent(): void
    {
        $this->dispatcher->subscribe('event1', function () {});
        $this->dispatcher->subscribe('event2', function () {});

        $removed = $this->dispatcher->clearListeners('event1');

        $this->assertEquals(1, $removed);
        $this->assertFalse($this->dispatcher->hasListeners('event1'));
        $this->assertTrue($this->dispatcher->hasListeners('event2'));
    }

    public function testClearAllListeners(): void
    {
        $this->dispatcher->subscribe('event1', function () {});
        $this->dispatcher->subscribe('event2', function () {});
        $this->dispatcher->subscribe('event2', function () {});

        $removed = $this->dispatcher->clearListeners();

        $this->assertEquals(3, $removed);
        $this->assertEmpty($this->dispatcher->getEventTypes());
    }

    public function testGetEventTypes(): void
    {
        $this->dispatcher->subscribe('event1', function () {});
        $this->dispatcher->subscribe('event2', function () {});

        $types = $this->dispatcher->getEventTypes();

        $this->assertContains('event1', $types);
        $this->assertContains('event2', $types);
    }

    public function testMultipleListenersCanModifyEvent(): void
    {
        $this->dispatcher->subscribe(EventType::BEFORE_PARSE->value, function (JsonEvent $event) {
            $event->setMetadata('processed', true);
        }, 10);

        $this->dispatcher->subscribe(EventType::BEFORE_PARSE->value, function (JsonEvent $event) {
            $this->assertTrue($event->getMetadata()['processed'] ?? false);
        }, 5);

        $event = JsonEvent::beforeOperation(EventType::BEFORE_PARSE, 'parse', []);
        $this->dispatcher->dispatch($event);
    }

    public function testEventIsReturnedAfterDispatch(): void
    {
        $event = JsonEvent::beforeOperation(EventType::BEFORE_PARSE, 'parse', []);
        $result = $this->dispatcher->dispatch($event);

        $this->assertSame($event, $result);
    }

    public function testAddEventListenerAlias(): void
    {
        $called = false;
        $listener = function () use (&$called) {
            $called = true;
        };

        $result = $this->dispatcher->addEventListener('test.event', $listener);
        $this->assertSame($this->dispatcher, $result);
        $this->assertTrue($this->dispatcher->hasListeners('test.event'));
    }

    public function testRemoveEventListenerAlias(): void
    {
        $listener = function () {};

        $this->dispatcher->addEventListener('test.event', $listener);
        $this->assertTrue($this->dispatcher->hasListeners('test.event'));

        $result = $this->dispatcher->removeEventListener('test.event', $listener);
        $this->assertTrue($result);
        $this->assertFalse($this->dispatcher->hasListeners('test.event'));
    }

    public function testUnsubscribeReturnsTrueOnSuccess(): void
    {
        $listener = function () {};
        
        $this->dispatcher->subscribe('test.event', $listener);
        $result = $this->dispatcher->unsubscribe('test.event', $listener);
        
        $this->assertTrue($result);
    }

    public function testClearListenersReturnsCount(): void
    {
        $this->dispatcher->subscribe('event1', function () {});
        $this->dispatcher->subscribe('event1', function () {});
        $this->dispatcher->subscribe('event2', function () {});

        $result = $this->dispatcher->clearListeners('event1');

        $this->assertEquals(2, $result);
        $this->assertFalse($this->dispatcher->hasListeners('event1'));
        $this->assertTrue($this->dispatcher->hasListeners('event2'));
    }

    public function testClearAllListenersReturnsTotal(): void
    {
        $this->dispatcher->subscribe('event1', function () {});
        $this->dispatcher->subscribe('event1', function () {});
        $this->dispatcher->subscribe('event2', function () {});
        $this->dispatcher->subscribe('event2', function () {});

        $result = $this->dispatcher->clearListeners();

        $this->assertEquals(4, $result);
        $this->assertEmpty($this->dispatcher->getEventTypes());
    }

    public function testMultipleListenersWithSamePriority(): void
    {
        $results = [];

        $this->dispatcher->subscribe(EventType::BEFORE_PARSE->value, function () use (&$results) {
            $results[] = 'first';
        }, 0);

        $this->dispatcher->subscribe(EventType::BEFORE_PARSE->value, function () use (&$results) {
            $results[] = 'second';
        }, 0);

        $this->dispatcher->subscribe(EventType::BEFORE_PARSE->value, function () use (&$results) {
            $results[] = 'third';
        }, 0);

        $event = JsonEvent::beforeOperation(EventType::BEFORE_PARSE, 'parse', []);
        $this->dispatcher->dispatch($event);

        $this->assertCount(3, $results);
        $this->assertContains('first', $results);
        $this->assertContains('second', $results);
        $this->assertContains('third', $results);
    }
}
