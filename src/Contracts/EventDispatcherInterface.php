<?php

declare(strict_types=1);

namespace Skpassegna\Json\Contracts;

use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;

/**
 * Extended PSR-14 compatible event dispatcher interface for JSON operations.
 *
 * Extends PSR-14 EventDispatcherInterface with JSON-specific features like
 * priority-based listeners, listener management, and event tracking.
 */
interface EventDispatcherInterface extends PsrEventDispatcherInterface
{
    /**
     * Subscribe a listener to an event type with optional priority.
     *
     * @param string $eventType The event type to listen for
     * @param callable(object): void $listener The listener callback
     * @param int $priority Priority for execution order (higher = earlier)
     *
     * @return self For method chaining
     */
    public function subscribe(string $eventType, callable $listener, int $priority = 0): self;

    /**
     * Unsubscribe a listener from an event type.
     *
     * @param string $eventType The event type
     * @param callable $listener The listener callback to remove
     *
     * @return bool True if the listener was found and removed, false otherwise
     */
    public function unsubscribe(string $eventType, callable $listener): bool;

    /**
     * Get all listeners for an event type.
     *
     * @param string $eventType The event type
     *
     * @return array<callable> List of listener callbacks
     */
    public function getListeners(string $eventType): array;

    /**
     * Check if there are listeners for an event type.
     *
     * @param string $eventType The event type
     *
     * @return bool True if listeners exist
     */
    public function hasListeners(string $eventType): bool;

    /**
     * Clear all listeners for an event type.
     *
     * @param string $eventType The event type (null to clear all)
     *
     * @return int The number of listeners removed
     */
    public function clearListeners(?string $eventType = null): int;

    /**
     * Get all registered event types.
     *
     * @return array<string> List of event type strings
     */
    public function getEventTypes(): array;

    /**
     * Alias for unsubscribe() for better naming convention compatibility.
     *
     * @param string $eventType The event type
     * @param callable $listener The listener callback to remove
     *
     * @return bool True if the listener was found and removed, false otherwise
     */
    public function removeEventListener(string $eventType, callable $listener): bool;

    /**
     * Alias for subscribe() for better naming convention compatibility.
     *
     * @param string $eventType The event type to listen for
     * @param callable(object): void $listener The listener callback
     * @param int $priority Priority for execution order (higher = earlier)
     *
     * @return self For method chaining
     */
    public function addEventListener(string $eventType, callable $listener, int $priority = 0): self;
}
