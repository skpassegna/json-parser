<?php

declare(strict_types=1);

namespace Skpassegna\Json\Contracts;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Immutable JSON event interface for lifecycle and operation events.
 *
 * Events represent significant moments in JSON operations with rich context
 * for debugging and monitoring purposes.
 */
interface JsonEventInterface extends StoppableEventInterface
{
    /**
     * Get the event type identifier.
     *
     * @return string
     */
    public function getEventType(): string;

    /**
     * Get the operation being performed (e.g., 'parse', 'validate', 'merge', 'diff').
     *
     * @return string
     */
    public function getOperation(): string;

    /**
     * Get the data being operated on.
     *
     * @return mixed
     */
    public function getData(): mixed;

    /**
     * Get optional context information about the operation.
     *
     * @return array<string, mixed>
     */
    public function getContext(): array;

    /**
     * Get optional error information if present.
     *
     * @return ?\Throwable
     */
    public function getError(): ?\Throwable;

    /**
     * Get any result data from the operation (for after-events).
     *
     * @return mixed
     */
    public function getResult(): mixed;

    /**
     * Get the timestamp when the event was created.
     *
     * @return float Unix timestamp with microseconds
     */
    public function getTimestamp(): float;

    /**
     * Get optional metadata about the event.
     *
     * @return array<string, mixed>
     */
    public function getMetadata(): array;

    /**
     * Set result data on the event (mutable during dispatch).
     *
     * @param mixed $result
     * @return void
     */
    public function setResult(mixed $result): void;

    /**
     * Set metadata on the event (mutable during dispatch).
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setMetadata(string $key, mixed $value): void;
}
