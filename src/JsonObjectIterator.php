<?php
namespace Skpassegna\JsonParser;

use Traversable;
use Iterator;

class JsonObjectIterator implements Iterator
{
    private $data;
    private $keys;
    private $position = 0;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->keys = array_keys($data);
    }

    public function current(): mixed
    {
        $key = $this->keys[$this->position];
        return $this->data[$key];
    }

    public function key(): mixed
    {
        return $this->keys[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return isset($this->keys[$this->position]);
    }
}