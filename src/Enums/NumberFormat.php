<?php

declare(strict_types=1);

namespace Skpassegna\Json\Enums;

enum NumberFormat: string
{
    case INTEGER = 'integer';
    case FLOAT = 'float';
    case DECIMAL = 'decimal';
    case SCIENTIFIC = 'scientific';
    case PERCENTAGE = 'percentage';

    public function isInteger(): bool
    {
        return $this === self::INTEGER;
    }

    public function isFloat(): bool
    {
        return $this === self::FLOAT;
    }

    public function isDecimal(): bool
    {
        return $this === self::DECIMAL;
    }
}
