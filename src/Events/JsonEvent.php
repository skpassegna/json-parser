<?php

declare(strict_types=1);

namespace Skpassegna\Json\Events;

use Skpassegna\Json\Contracts\JsonEventInterface;
use Skpassegna\Json\Enums\EventType;

/**
 * Immutable JSON event for lifecycle monitoring.
 *
 * Events contain rich context about operations for logging, monitoring,
 * and validation purposes while maintaining immutability for thread-safety.
 */
final class JsonEvent implements JsonEventInterface
{
    private bool $propagationStopped = false;

    /**
     * @var array<string, mixed>
     */
    private array $metadata = [];

    /**
     * @var mixed
     */
    private mixed $result = null;

    /**
     * @var float
     */
    private float $timestamp;

    public function __construct(
        private readonly EventType $eventType,
        private readonly string $operation,
        private readonly mixed $data,
        private readonly ?\Throwable $error = null,
        private readonly array $context = [],
        float $timestamp = 0.0,
    ) {
        $this->timestamp = $timestamp ?: hrtime(true) / 1_000_000_000;
    }

    public function getEventType(): string
    {
        return $this->eventType->value;
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getError(): ?\Throwable
    {
        return $this->error;
    }

    public function getResult(): mixed
    {
        return $this->result;
    }

    public function getTimestamp(): float
    {
        return $this->timestamp;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setResult(mixed $result): void
    {
        $this->result = $result;
    }

    public function setMetadata(string $key, mixed $value): void
    {
        $this->metadata[$key] = $value;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }

    /**
     * Create a before-event for an operation.
     *
     * @param EventType $eventType
     * @param string $operation
     * @param mixed $data
     * @param array<string, mixed> $context
     * @return self
     */
    public static function beforeOperation(
        EventType $eventType,
        string $operation,
        mixed $data,
        array $context = []
    ): self {
        return new self($eventType, $operation, $data, context: $context);
    }

    /**
     * Create an after-event for an operation.
     *
     * @param EventType $eventType
     * @param string $operation
     * @param mixed $data
     * @param mixed $result
     * @param array<string, mixed> $context
     * @return self
     */
    public static function afterOperation(
        EventType $eventType,
        string $operation,
        mixed $data,
        mixed $result,
        array $context = []
    ): self {
        $event = new self($eventType, $operation, $data, context: $context);
        $event->result = $result;
        return $event;
    }

    /**
     * Create an error event.
     *
     * @param EventType $eventType
     * @param string $operation
     * @param mixed $data
     * @param \Throwable $error
     * @param array<string, mixed> $context
     * @return self
     */
    public static function onError(
        EventType $eventType,
        string $operation,
        mixed $data,
        \Throwable $error,
        array $context = []
    ): self {
        return new self($eventType, $operation, $data, $error, $context);
    }

    /**
     * Get a JSON-serializable representation.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'event_type' => $this->eventType->value,
            'operation' => $this->operation,
            'timestamp' => $this->timestamp,
            'propagation_stopped' => $this->propagationStopped,
            'context' => $this->context,
            'metadata' => $this->metadata,
            'has_error' => $this->error !== null,
            'error' => $this->error ? [
                'class' => $this->error::class,
                'message' => $this->error->getMessage(),
                'code' => $this->error->getCode(),
            ] : null,
        ];
    }
}
