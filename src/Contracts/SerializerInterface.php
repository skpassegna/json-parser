<?php

declare(strict_types=1);

namespace Skpassegna\Json\Contracts;

interface SerializerInterface
{
    /**
     * Serialize data to JSON string
     *
     * @param mixed $data The data to serialize
     * @param int $flags JSON encoding options
     * @param int $depth Maximum depth. Must be greater than zero
     * @return string The JSON encoded string
     * @throws \Skpassegna\Json\Exceptions\RuntimeException If encoding fails
     */
    public function serialize(mixed $data, int $flags = 0, int $depth = 512): string;

    /**
     * Deserialize JSON string to PHP value
     *
     * @param string $json The JSON string
     * @param bool $associative When true, objects will be converted to associative arrays
     * @param int $depth Maximum depth. Must be greater than zero
     * @param int $flags JSON decoding options
     * @return mixed The decoded value
     * @throws \Skpassegna\Json\Exceptions\RuntimeException If decoding fails
     */
    public function deserialize(string $json, bool $associative = false, int $depth = 512, int $flags = 0): mixed;
}
