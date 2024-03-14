<?php

namespace skpassegna\JsonParser;

use Skpassegna\JsonParser\JsonValue;
use Skpassegna\JsonParser\JsonObject;
use Skpassegna\JsonParser\JsonArray;
use Skpassegna\JsonParser\Exceptions\HumanReadableJsonException;
use Skpassegna\JsonParser\Contracts\JsonIterable;
use Skpassegna\JsonParser\Contracts\JsonAccessible;

class JsonParser
{
    /**
     * Parse a JSON string into a JsonObject or JsonArray instance.
     *
     * @param string $jsonString The JSON string to parse.
     * @return JsonObject|JsonArray
     * @throws HumanReadableJsonException If the JSON string is invalid.
     */
    public function parse(string $jsonString)
    {
        $data = json_decode($jsonString, true);

        $errorCode = json_last_error();
        if ($errorCode !== JSON_ERROR_NONE) {
            $errorMessage = json_last_error_msg();
            throw new HumanReadableJsonException($errorCode, $errorMessage);
        }

        return $this->createObject($data);
    }

    /**
     * Create a JsonObject or JsonArray instance from the given data.
     *
     * @param array|null $data The data to create the object or array from.
     * @return JsonObject|JsonArray
     */
    private function createObject($data)
    {
        if (is_array($data)) {
            $tmp = array_values($data);
            return $tmp === $data ? new JsonArray($data) : new JsonObject($data);
        } elseif (is_null($data)) {
            return new JsonValue(null);
        } elseif (is_bool($data)) {
            return new JsonValue($data);
        } elseif (is_numeric($data)) {
            return new JsonValue($data + 0); // Convert to float if possible
        } else {
            return new JsonValue($data);
        }
    }
}