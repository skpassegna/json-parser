<?php

declare(strict_types=1);

namespace Skpassegna\Json\Enums;

enum JsonMergeStrategy: string
{
    case RECURSIVE = 'recursive';
    case REPLACE = 'replace';
    case SHALLOW = 'shallow';
    case DEEP = 'deep';
    case COMBINE = 'combine';

    public function isRecursive(): bool
    {
        return $this === self::RECURSIVE || $this === self::DEEP;
    }

    public function isReplacive(): bool
    {
        return $this === self::REPLACE;
    }
}
