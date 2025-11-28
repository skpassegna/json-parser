<?php

declare(strict_types=1);

namespace Skpassegna\Json\Services;

use Skpassegna\Json\Exceptions\InvalidArgumentException;
use Skpassegna\Json\Enums\NumberFormat;

final class TypeCoercionService
{
    private bool $strict;

    public function __construct(bool $strict = false)
    {
        $this->strict = $strict;
    }

    public function coerceToString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_null($value)) {
            return $this->strict ? throw new InvalidArgumentException('Cannot coerce null to string in strict mode') : '';
        }

        if (is_array($value) || is_object($value)) {
            return $this->strict ? throw new InvalidArgumentException('Cannot coerce array/object to string in strict mode') : json_encode($value);
        }

        return (string)$value;
    }

    public function coerceToInt(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        if (is_float($value)) {
            return (int)$value;
        }

        if (is_string($value)) {
            if ('' === $value) {
                return $this->strict ? throw new InvalidArgumentException('Cannot coerce empty string to int in strict mode') : 0;
            }
            if (!is_numeric($value)) {
                return $this->strict ? throw new InvalidArgumentException('Cannot coerce non-numeric string to int in strict mode') : 0;
            }
            return (int)$value;
        }

        if (is_null($value)) {
            return $this->strict ? throw new InvalidArgumentException('Cannot coerce null to int in strict mode') : 0;
        }

        if (is_array($value) || is_object($value)) {
            return $this->strict ? throw new InvalidArgumentException('Cannot coerce array/object to int in strict mode') : (empty((array)$value) ? 0 : 1);
        }

        return (int)$value;
    }

    public function coerceToFloat(mixed $value): float
    {
        if (is_float($value)) {
            return $value;
        }

        if (is_int($value)) {
            return (float)$value;
        }

        if (is_bool($value)) {
            return $value ? 1.0 : 0.0;
        }

        if (is_string($value)) {
            if ('' === $value) {
                return $this->strict ? throw new InvalidArgumentException('Cannot coerce empty string to float in strict mode') : 0.0;
            }
            if (!is_numeric($value)) {
                return $this->strict ? throw new InvalidArgumentException('Cannot coerce non-numeric string to float in strict mode') : 0.0;
            }
            return (float)$value;
        }

        if (is_null($value)) {
            return $this->strict ? throw new InvalidArgumentException('Cannot coerce null to float in strict mode') : 0.0;
        }

        if (is_array($value) || is_object($value)) {
            return $this->strict ? throw new InvalidArgumentException('Cannot coerce array/object to float in strict mode') : (empty((array)$value) ? 0.0 : 1.0);
        }

        return (float)$value;
    }

    public function coerceToBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            return $value !== 0;
        }

        if (is_float($value)) {
            return $value !== 0.0;
        }

        if (is_string($value)) {
            $lower = strtolower(trim($value));
            if ('' === $lower || '0' === $lower) {
                return false;
            }
            if ('true' === $lower || '1' === $lower || 'yes' === $lower) {
                return true;
            }
            return $this->strict ? throw new InvalidArgumentException("Cannot coerce string '{$value}' to bool in strict mode") : true;
        }

        if (is_null($value)) {
            return false;
        }

        if (is_array($value) || is_object($value)) {
            return !empty((array)$value);
        }

        return (bool)$value;
    }

    public function coerceToArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_object($value)) {
            return (array)$value;
        }

        if (is_null($value)) {
            return $this->strict ? throw new InvalidArgumentException('Cannot coerce null to array in strict mode') : [];
        }

        return [$value];
    }

    public function coerceToObject(mixed $value): object
    {
        if (is_object($value)) {
            return $value;
        }

        if (is_array($value)) {
            return (object)$value;
        }

        if (is_null($value)) {
            return $this->strict ? throw new InvalidArgumentException('Cannot coerce null to object in strict mode') : new \stdClass();
        }

        $obj = new \stdClass();
        $obj->value = $value;
        return $obj;
    }

    public function coerceToNull(mixed $value): null
    {
        return null;
    }

    public function normalizeScalar(mixed $value, string $targetType): mixed
    {
        return match ($targetType) {
            'string' => $this->coerceToString($value),
            'int', 'integer' => $this->coerceToInt($value),
            'float', 'double' => $this->coerceToFloat($value),
            'bool', 'boolean' => $this->coerceToBool($value),
            'array' => $this->coerceToArray($value),
            'object' => $this->coerceToObject($value),
            'null' => $this->coerceToNull($value),
            default => throw new InvalidArgumentException("Unsupported type coercion target: {$targetType}"),
        };
    }

    public function isStrict(): bool
    {
        return $this->strict;
    }

    public function setStrict(bool $strict): void
    {
        $this->strict = $strict;
    }
}
