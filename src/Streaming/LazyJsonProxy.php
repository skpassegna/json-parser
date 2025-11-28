<?php

declare(strict_types=1);

namespace Skpassegna\Json\Streaming;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;
use Skpassegna\Json\Contracts\LazyProxyInterface;
use Skpassegna\Json\Exceptions\ParseException;

final class LazyJsonProxy implements 
    LazyProxyInterface, 
    ArrayAccess, 
    IteratorAggregate, 
    Countable
{
    /**
     * @var callable
     */
    private $loader;

    /**
     * @var mixed
     */
    private mixed $data = null;

    /**
     * @var bool
     */
    private bool $loaded = false;

    /**
     * @var bool
     */
    private bool $prefetch;

    /**
     * Create a new lazy proxy.
     *
     * @param callable $loader Callable that returns the decoded data
     * @param bool $prefetch Whether to prefetch on first access
     */
    public function __construct(callable $loader, bool $prefetch = false)
    {
        $this->loader = $loader;
        $this->prefetch = $prefetch;

        if ($prefetch) {
            $this->load();
        }
    }

    /**
     * @inheritDoc
     */
    public function isLoaded(): bool
    {
        return $this->loaded;
    }

    /**
     * @inheritDoc
     */
    public function load(): void
    {
        if ($this->loaded) {
            return;
        }

        try {
            $this->data = ($this->loader)();
            $this->loaded = true;
        } catch (\Throwable $e) {
            throw new ParseException("Failed to load lazy data: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getData(): mixed
    {
        if (!$this->loaded) {
            $this->load();
        }

        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function reset(): void
    {
        $this->data = null;
        $this->loaded = false;
    }

    /**
     * Magic method for property access.
     */
    public function __get(string $name): mixed
    {
        $data = $this->getData();

        if (is_array($data) && isset($data[$name])) {
            return $data[$name];
        }

        if (is_object($data) && property_exists($data, $name)) {
            return $data->$name;
        }

        return null;
    }

    /**
     * Magic method for checking property existence.
     */
    public function __isset(string $name): bool
    {
        $data = $this->getData();

        if (is_array($data)) {
            return isset($data[$name]);
        }

        if (is_object($data)) {
            return property_exists($data, $name);
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $offset): bool
    {
        $data = $this->getData();

        if (is_array($data)) {
            return isset($data[$offset]);
        }

        if (is_object($data) && property_exists($data, (string) $offset)) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        $data = $this->getData();

        if (is_array($data) && isset($data[$offset])) {
            return $data[$offset];
        }

        if (is_object($data) && property_exists($data, (string) $offset)) {
            return $data->{$offset};
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $data = $this->getData();

        if (is_array($data)) {
            if ($offset === null) {
                $this->data[] = $value;
            } else {
                $this->data[$offset] = $value;
            }
        } elseif (is_object($data)) {
            $data->{$offset} = $value;
            $this->data = $data;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset): void
    {
        $data = $this->getData();

        if (is_array($data) && isset($data[$offset])) {
            unset($this->data[$offset]);
        } elseif (is_object($data) && property_exists($data, (string) $offset)) {
            unset($data->{$offset});
            $this->data = $data;
        }
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Iterator
    {
        $data = $this->getData();

        if (is_array($data)) {
            return new ArrayIterator($data);
        }

        if (is_object($data)) {
            $vars = get_object_vars($data);
            return new ArrayIterator($vars);
        }

        return new ArrayIterator([]);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        $data = $this->getData();

        if (is_array($data)) {
            return count($data);
        }

        if (is_object($data)) {
            return count(get_object_vars($data));
        }

        return 0;
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        $data = $this->getData();
        return json_encode($data) ?: '{}';
    }
}
