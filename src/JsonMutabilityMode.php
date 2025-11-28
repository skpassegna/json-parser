<?php

declare(strict_types=1);

namespace Skpassegna\Json;

enum JsonMutabilityMode
{
    case MUTABLE;
    case IMMUTABLE;

    public function isMutable(): bool
    {
        return $this === self::MUTABLE;
    }

    public function isImmutable(): bool
    {
        return $this === self::IMMUTABLE;
    }
}
