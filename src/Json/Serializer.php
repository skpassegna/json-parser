<?php

declare(strict_types=1);

namespace Skpassegna\Json\Json;

use Skpassegna\Json\Contracts\SerializerInterface;
use Skpassegna\Json\Exceptions\RuntimeException;

class Serializer implements SerializerInterface
{
    /**
     * @inheritDoc
     */
    public function serialize(mixed $data, int $flags = 0, int $depth = 512): string
    {
        try {
            $result = json_encode($data, $flags | JSON_THROW_ON_ERROR, $depth);
            
            if ($result === false) {
                throw new RuntimeException('Failed to encode data to JSON');
            }
            
            return $result;
        } catch (\JsonException $e) {
            throw new RuntimeException(
                sprintf('JSON encoding error: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function deserialize(string $json, bool $associative = false, int $depth = 512, int $flags = 0): mixed
    {
        try {
            return json_decode($json, $associative, $depth, $flags | JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new RuntimeException(
                sprintf('JSON decoding error: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }
}
