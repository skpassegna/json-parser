<?php

declare(strict_types=1);

namespace Skpassegna\Json\Enums;

enum TraversalMode: string
{
    case BREADTH_FIRST = 'breadth_first';
    case DEPTH_FIRST = 'depth_first';
    case LAZY = 'lazy';
    case STRICT = 'strict';

    public function isBreadthFirst(): bool
    {
        return $this === self::BREADTH_FIRST;
    }

    public function isDepthFirst(): bool
    {
        return $this === self::DEPTH_FIRST;
    }

    public function isLazy(): bool
    {
        return $this === self::LAZY;
    }

    public function isStrict(): bool
    {
        return $this === self::STRICT;
    }
}
