<?php

namespace Skpassegna\JsonParser\Contracts;

interface JsonIterable extends \IteratorAggregate
{
    /**
     * Get an iterator for the object properties.
     *
     * @return \Traversable An iterator for the object properties.
     */
    public function getIterator(): \Traversable;
}