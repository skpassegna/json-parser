<?php

declare(strict_types=1);

namespace Skpassegna\Json\Contracts;

interface LazyProxyInterface
{
    /**
     * Check if the proxy data has been loaded/decoded.
     *
     * @return bool
     */
    public function isLoaded(): bool;

    /**
     * Force decode/load the underlying data immediately.
     *
     * @return void
     */
    public function load(): void;

    /**
     * Get the underlying decoded data.
     *
     * @return mixed
     */
    public function getData(): mixed;

    /**
     * Reset to unloaded state (for re-initialization).
     *
     * @return void
     */
    public function reset(): void;
}
