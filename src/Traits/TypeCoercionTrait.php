<?php

declare(strict_types=1);

namespace Skpassegna\Json\Traits;

use Skpassegna\Json\Services\TypeCoercionService;

trait TypeCoercionTrait
{
    protected ?TypeCoercionService $typeCoercionService = null;

    public function getTypeCoercionService(): TypeCoercionService
    {
        if ($this->typeCoercionService === null) {
            $this->typeCoercionService = new TypeCoercionService(false);
        }
        return $this->typeCoercionService;
    }

    public function setTypeCoercionService(TypeCoercionService $service): self
    {
        $this->typeCoercionService = $service;
        return $this;
    }

    public function enableStrictCoercion(bool $strict = true): self
    {
        $this->getTypeCoercionService()->setStrict($strict);
        return $this;
    }

    public function coerceString(mixed $value): string
    {
        return $this->getTypeCoercionService()->coerceToString($value);
    }

    public function coerceInt(mixed $value): int
    {
        return $this->getTypeCoercionService()->coerceToInt($value);
    }

    public function coerceFloat(mixed $value): float
    {
        return $this->getTypeCoercionService()->coerceToFloat($value);
    }

    public function coerceBool(mixed $value): bool
    {
        return $this->getTypeCoercionService()->coerceToBool($value);
    }

    public function coerceArrayType(mixed $value): array
    {
        return $this->getTypeCoercionService()->coerceToArray($value);
    }

    public function coerceObject(mixed $value): object
    {
        return $this->getTypeCoercionService()->coerceToObject($value);
    }

    public function normalizeScalar(mixed $value, string $targetType): mixed
    {
        return $this->getTypeCoercionService()->normalizeScalar($value, $targetType);
    }
}
