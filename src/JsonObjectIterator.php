<?php
namespace Skpassegna\JsonParser;

class JsonObjectIterator implements \Traversable
{
    private $data;
    private $keys;
    private $position = 0;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->keys = array_keys($data);
    }

    public function current()
    {
        $key = $this->keys[$this->position];
        return $this->data[$key];
    }

    public function key()
    {
        return $this->keys[$this->position];
    }

    public function next()
    {
        ++$this->position;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function valid()
    {
        return isset($this->keys[$this->position]);
    }
}