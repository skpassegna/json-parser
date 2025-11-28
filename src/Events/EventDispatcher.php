<?php

declare(strict_types=1);

namespace Skpassegna\Json\Events;

use Psr\EventDispatcher\StoppableEventInterface;
use Skpassegna\Json\Contracts\EventDispatcherInterface;
use Skpassegna\Json\Contracts\JsonEventInterface;

/**
 * Lightweight PSR-14 compatible event dispatcher for JSON operations.
 *
 * Supports priority-based listener execution, listener management,
 * and JSON-specific event patterns with minimal overhead.
 */
final class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var array<string, array<int, callable>>
     */
    private array $listeners = [];

    /**
     * @var array<string, int>
     */
    private array $listenerCounts = [];

    public function dispatch(object $event): object
    {
        if (!$event instanceof StoppableEventInterface) {
            return $event;
        }

        $eventType = $event instanceof JsonEventInterface
            ? $event->getEventType()
            : self::getEventTypeFromObject($event);

        if (!isset($this->listeners[$eventType])) {
            return $event;
        }

        foreach ($this->listeners[$eventType] as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }

            $listener($event);
        }

        return $event;
    }

    public function subscribe(string $eventType, callable $listener, int $priority = 0): void
    {
        if (!isset($this->listeners[$eventType])) {
            $this->listeners[$eventType] = [];
            $this->listenerCounts[$eventType] = 0;
        }

        $this->listeners[$eventType][$priority] = $listener;
        $this->listenerCounts[$eventType]++;

        krsort($this->listeners[$eventType], SORT_NUMERIC);
    }

    public function unsubscribe(string $eventType, callable $listener): bool
    {
        if (!isset($this->listeners[$eventType])) {
            return false;
        }

        foreach ($this->listeners[$eventType] as $priority => $existingListener) {
            if ($existingListener === $listener) {
                unset($this->listeners[$eventType][$priority]);
                $this->listenerCounts[$eventType]--;

                if (empty($this->listeners[$eventType])) {
                    unset($this->listeners[$eventType]);
                    unset($this->listenerCounts[$eventType]);
                }

                return true;
            }
        }

        return false;
    }

    public function getListeners(string $eventType): array
    {
        return $this->listeners[$eventType] ?? [];
    }

    public function hasListeners(string $eventType): bool
    {
        return isset($this->listeners[$eventType]) && !empty($this->listeners[$eventType]);
    }

    public function clearListeners(string $eventType = ''): int
    {
        if ($eventType === '') {
            $count = array_sum($this->listenerCounts);
            $this->listeners = [];
            $this->listenerCounts = [];
            return $count;
        }

        if (!isset($this->listeners[$eventType])) {
            return 0;
        }

        $count = $this->listenerCounts[$eventType] ?? 0;
        unset($this->listeners[$eventType]);
        unset($this->listenerCounts[$eventType]);

        return $count;
    }

    public function getEventTypes(): array
    {
        return array_keys($this->listeners);
    }

    /**
     * Get event type from a generic object.
     *
     * @param object $event
     * @return string
     */
    private static function getEventTypeFromObject(object $event): string
    {
        if (method_exists($event, 'getEventType')) {
            return $event->getEventType();
        }

        return $event::class;
    }
}
