<?php

declare(strict_types=1);

namespace Skpassegna\Json\Enums;

/**
 * Advanced diff and merge strategy enum.
 *
 * Defines strategies for comparing and merging JSON documents
 * with support for RFC 6902 (JSON Patch) and RFC 7396 (JSON Merge Patch).
 */
enum DiffMergeStrategy: string
{
    // Merge strategies
    case MERGE_RECURSIVE = 'merge.recursive';
    case MERGE_DEEP = 'merge.deep';
    case MERGE_REPLACE = 'merge.replace';
    case MERGE_SHALLOW = 'merge.shallow';
    case MERGE_PATCH_RFC7396 = 'merge.patch.rfc7396';
    case MERGE_CONFLICT_AWARE = 'merge.conflict_aware';

    // Diff strategies
    case DIFF_STRUCTURAL = 'diff.structural';
    case DIFF_RFC6902_PATCH = 'diff.rfc6902.patch';
    case DIFF_DETAILED = 'diff.detailed';
    case DIFF_SUMMARY = 'diff.summary';

    public function isMergeStrategy(): bool
    {
        return str_starts_with($this->value, 'merge.');
    }

    public function isDiffStrategy(): bool
    {
        return str_starts_with($this->value, 'diff.');
    }

    public function isDeep(): bool
    {
        return $this === self::MERGE_DEEP || $this === self::MERGE_RECURSIVE;
    }

    public function supportsConflictDetection(): bool
    {
        return $this === self::MERGE_CONFLICT_AWARE;
    }

    public function isRfcCompliant(): bool
    {
        return $this === self::MERGE_PATCH_RFC7396 || $this === self::DIFF_RFC6902_PATCH;
    }
}
