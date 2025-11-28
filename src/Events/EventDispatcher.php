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

    public function subscribe(string $eventType, callable $listener, int $priority = 0): self
    {
        if (!isset($this->listeners[$eventType])) {
            $this->listeners[$eventType] = [];
            $this->listenerCounts[$eventType] = 0;
        }

        // Handle multiple listeners with same priority by using negative float offset
        // This maintains insertion order while respecting priority levels
        // Higher priority numbers execute first, but within same priority, insertion order is preserved
        $key = (float)$priority;
        $counter = 0;
        while (isset($this->listeners[$eventType][$key])) {
            $counter++;
            // Subtract a small fraction to maintain insertion order within same priority
            // but still sort correctly with different priorities
            $key = $priority - ($counter / 100000);
        }

        $this->listeners[$eventType][$key] = $listener;
        $this->listenerCounts[$eventType]++;

        krsort($this->listeners[$eventType], SORT_NUMERIC);

        return $this;
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

    public function clearListeners(?string $eventType = null): int
    {
        if ($eventType === null) {
            $total = array_sum($this->listenerCounts);
            $this->listeners = [];
            $this->listenerCounts = [];
            return $total;
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
     * Alias for removeEventListener to maintain interface contract.
     *
     * @param string $eventType The event type
     * @param callable $listener The listener callback to remove
     *
     * @return bool True if the listener was found and removed, false otherwise
     */
    public function removeEventListener(string $eventType, callable $listener): bool
    {
        return $this->unsubscribe($eventType, $listener);
    }

    /**
     * Alias for addEventListener to maintain interface contract.
     *
     * @param string $eventType The event type to listen for
     * @param callable $listener The listener callback
     * @param int $priority Priority for execution order (higher = earlier)
     *
     * @return self For method chaining
     */
    public function addEventListener(string $eventType, callable $listener, int $priority = 0): self
    {
        return $this->subscribe($eventType, $listener, $priority);
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
