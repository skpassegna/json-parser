<?php

namespace Skpassegna\JsonParser\Contracts;

interface JsonSerializer
{
    /**
     * Serialize a value to JSON.
     *
     * @param mixed $value The value to serialize.
     * @return string The JSON representation of the value.
     */
    public function serialize($value): string;
}