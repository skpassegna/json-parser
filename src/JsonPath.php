<?php

namespace Skpassegna\JsonParser;

use Skpassegna\JsonParser\Exceptions\InvalidJsonPathException;

class JsonPath
{
    public static function get($data, string $jsonPath)
    {
        $parts = preg_split('/(?<=\\\\.)|(?<!\\\\)\./', $jsonPath);
        $result = $data;

        foreach ($parts as $part) {
            $part = str_replace('\\.', '.', $part);

            if ($result instanceof JsonObject) {
                if (!$result->has($part)) {
                    throw new InvalidJsonPathException("Invalid JSON Path: '{$jsonPath}'");
                }
                $result = $result->get($part);
            } elseif ($result instanceof JsonArray) {
                if (!is_numeric($part)) {
                    throw new InvalidJsonPathException("Invalid JSON Path: '{$jsonPath}'");
                }
                $result = $result->offsetGet($part);
            } else {
                throw new InvalidJsonPathException("Invalid JSON Path: '{$jsonPath}'");
            }
        }

        return $result;
    }
}