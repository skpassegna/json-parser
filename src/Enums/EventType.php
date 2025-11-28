<?php

declare(strict_types=1);

namespace Skpassegna\Json\Enums;

/**
 * JSON operation event types.
 *
 * These represent significant lifecycle moments in JSON operations
 * that listeners can subscribe to for monitoring and customization.
 */
enum EventType: string
{
    // Parsing events
    case BEFORE_PARSE = 'before.parse';
    case AFTER_PARSE = 'after.parse';
    case PARSE_ERROR = 'parse.error';

    // Serialization events
    case BEFORE_SERIALIZE = 'before.serialize';
    case AFTER_SERIALIZE = 'after.serialize';
    case SERIALIZE_ERROR = 'serialize.error';

    // Validation events
    case BEFORE_VALIDATE = 'before.validate';
    case AFTER_VALIDATE = 'after.validate';
    case VALIDATE_ERROR = 'validate.error';

    // Mutation events
    case BEFORE_MUTATE = 'before.mutate';
    case AFTER_MUTATE = 'after.mutate';
    case MUTATE_ERROR = 'mutate.error';

    // Diff/Merge events
    case BEFORE_DIFF = 'before.diff';
    case AFTER_DIFF = 'after.diff';
    case BEFORE_MERGE = 'before.merge';
    case AFTER_MERGE = 'after.merge';
    case MERGE_ERROR = 'merge.error';

    // Path/Pointer operations
    case BEFORE_PATH_OPERATION = 'before.path_operation';
    case AFTER_PATH_OPERATION = 'after.path_operation';
    case PATH_ERROR = 'path.error';

    // Generic events
    case ON_ERROR = 'on.error';
    case ON_WARNING = 'on.warning';

    public function getOperation(): string
    {
        return match ($this) {
            self::BEFORE_PARSE, self::AFTER_PARSE, self::PARSE_ERROR => 'parse',
            self::BEFORE_SERIALIZE, self::AFTER_SERIALIZE, self::SERIALIZE_ERROR => 'serialize',
            self::BEFORE_VALIDATE, self::AFTER_VALIDATE, self::VALIDATE_ERROR => 'validate',
            self::BEFORE_MUTATE, self::AFTER_MUTATE, self::MUTATE_ERROR => 'mutate',
            self::BEFORE_DIFF, self::AFTER_DIFF => 'diff',
            self::BEFORE_MERGE, self::AFTER_MERGE, self::MERGE_ERROR => 'merge',
            self::BEFORE_PATH_OPERATION, self::AFTER_PATH_OPERATION, self::PATH_ERROR => 'path_operation',
            self::ON_ERROR => 'error',
            self::ON_WARNING => 'warning',
        };
    }

    public function isBeforeEvent(): bool
    {
        return str_starts_with($this->value, 'before.');
    }

    public function isAfterEvent(): bool
    {
        return str_starts_with($this->value, 'after.');
    }

    public function isErrorEvent(): bool
    {
        return str_contains($this->value, 'error') || $this === self::ON_ERROR;
    }

    public function getCorrespondingBeforeEvent(): ?self
    {
        return match ($this) {
            self::AFTER_PARSE, self::PARSE_ERROR => self::BEFORE_PARSE,
            self::AFTER_SERIALIZE, self::SERIALIZE_ERROR => self::BEFORE_SERIALIZE,
            self::AFTER_VALIDATE, self::VALIDATE_ERROR => self::BEFORE_VALIDATE,
            self::AFTER_MUTATE, self::MUTATE_ERROR => self::BEFORE_MUTATE,
            self::AFTER_MERGE, self::MERGE_ERROR => self::BEFORE_MERGE,
            self::AFTER_DIFF => self::BEFORE_DIFF,
            self::AFTER_PATH_OPERATION, self::PATH_ERROR => self::BEFORE_PATH_OPERATION,
            default => null,
        };
    }
}
