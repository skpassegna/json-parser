<?php

namespace skpassegna\JsonParser;

use Skpassegna\JsonParser\JsonValue;
use Skpassegna\JsonParser\JsonObject;
use Skpassegna\JsonParser\JsonArray;
use Skpassegna\JsonParser\Exceptions\MalformedJsonException;
use Skpassegna\JsonParser\Exceptions\JsonParseException;
use Skpassegna\JsonParser\Exceptions\InvalidJsonException;
use Skpassegna\JsonParser\Exceptions\HumanReadableJsonException;
use Skpassegna\JsonParser\Contracts\JsonSerializer;
use Skpassegna\JsonParser\Contracts\JsonIterable;
use Skpassegna\JsonParser\Contracts\JsonDeserializer;
use Skpassegna\JsonParser\Contracts\JsonAccessible;

class JsonParser
{
    private $serializers = [];
    private $deserializers = [];

    /**
     * Parse a JSON string into a JsonObject or JsonArray instance.
     *
     * @param string $jsonString The JSON string to parse.
     * @return JsonObject|JsonArray
     * @throws HumanReadableJsonException If the JSON string is invalid.
     */
    public function parse(string $jsonString)
    {
        $data = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $errorMessage = json_last_error_msg();
            $errorCode = json_last_error();

            switch ($errorCode) {
                case JSON_ERROR_DEPTH:
                case JSON_ERROR_STATE_MISMATCH:
                case JSON_ERROR_CTRL_CHAR:
                case JSON_ERROR_SYNTAX:
                    throw new MalformedJsonException($errorMessage, $errorCode);
                case JSON_ERROR_UTF8:
                    throw new InvalidJsonException($errorMessage, $errorCode);
                default:
                    throw new JsonParseException($errorMessage, json_last_error_position()->line, json_last_error_position()->column, $errorCode);
            }
        }

        return $this->createObject($data);
    }

    /**
     * Register a custom JSON serializer for a specific class.
     *
     * @param string $className The class name.
     * @param JsonSerializer $serializer The JSON serializer instance.
     */
    public function registerSerializer(string $className, JsonSerializer $serializer): void
    {
        $this->serializers[$className] = $serializer;
    }

    /**
     * Get the registered JSON serializer for a specific class.
     *
     * @param string $className The class name.
     * @return JsonSerializer|null The JSON serializer instance, or null if not registered.
     */
    public function getSerializer(string $className): ?JsonSerializer
    {
        return $this->serializers[$className] ?? null;
    }

    /**
     * Register a custom JSON deserializer for a specific data type.
     *
     * @param string $dataType The data type identifier.
     * @param JsonDeserializer $deserializer The JSON deserializer instance.
     */
    public function registerDeserializer(string $dataType, JsonDeserializer $deserializer): void
    {
        $this->deserializers[$dataType] = $deserializer;
    }

    /**
     * Get the registered JSON deserializer for a specific data type.
     *
     * @param string $dataType The data type identifier.
     * @return JsonDeserializer|null The JSON deserializer instance, or null if not registered.
     */
    public function getDeserializer(string $dataType): ?JsonDeserializer
    {
        return $this->deserializers[$dataType] ?? null;
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
            if ($tmp === $data) {
                return new JsonArray(array_map([$this, 'createObject'], $data));
            } else {
                $object = new JsonObject();
                foreach ($data as $key => $value) {
                    $object->set($key, $this->createObject($value));
                }
                return $object;
            }
        } elseif (is_null($data)) {
            return new JsonValue(null);
        } elseif (is_bool($data)) {
            return new JsonValue($data);
        } elseif (is_numeric($data)) {
            return new JsonValue($data + 0); // Convert to float if possible
        } elseif (is_string($data)) {
            // Check if a custom deserializer is registered for this data type
            $deserializer = $this->getDeserializer($data);
            if ($deserializer !== null) {
                return $deserializer->deserialize($data);
            }
            return new JsonValue($data);
        } else {
            // Check if a custom serializer is registered for this data type
            $serializer = $this->getSerializer(get_class($data));
            if ($serializer !== null) {
                $jsonString = $serializer->serialize($data);
                return $this->parse($jsonString);
            }
            throw new \Exception('Unsupported data type for serialization.');
        }
    }

    /**
     * Check if a JSON string is valid.
     *
     * @param string $jsonString The JSON string to validate.
     * @return bool True if the JSON string is valid, false otherwise.
     */
    public function isValid(string $jsonString): bool
    {
        json_decode($jsonString);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Attempt to fix an invalid JSON string.
     *
     * @param string $jsonString The invalid JSON string.
     * @return string The fixed JSON string, or the original string if it cannot be fixed.
     * @throws HumanReadableJsonException If the JSON string cannot be fixed.
     */
    public function fix(string $jsonString): string
    {
        $fixedJsonString = $this->removeInvalidCharacters($jsonString);
        $fixedJsonString = $this->fixUnbalancedBrackets($fixedJsonString);
        $fixedJsonString = $this->fixInvalidEscapes($fixedJsonString);
        $fixedJsonString = $this->fixMissingQuotes($fixedJsonString);
        $fixedJsonString = $this->fixTrailingCommas($fixedJsonString);

        if (!$this->isValid($fixedJsonString)) {
            throw new HumanReadableJsonException(JSON_ERROR_SYNTAX, 'The JSON string is invalid and cannot be fixed.');
        }

        return $fixedJsonString;
    }

    private function removeInvalidCharacters(string $jsonString): string
    {
        $pattern = '/[\x00-\x08\x0B\x0C\x0E-\x1F]/';
        return preg_replace($pattern, '', $jsonString);
    }

    private function fixUnbalancedBrackets(string $jsonString): string
    {
        $openBrackets = ['[', '{'];
        $closeBrackets = [']', '}'];
        $stack = [];
        $fixedJsonString = '';

        foreach (str_split($jsonString) as $char) {
            if (in_array($char, $openBrackets)) {
                $stack[] = $char;
            } elseif (in_array($char, $closeBrackets)) {
                $openingBracket = array_pop($stack);
                $closingBracket = array_search($char, $closeBrackets);
                $openingBracket = array_search($openingBracket, $openBrackets);

                if ($closingBracket !== $openingBracket) {
                    $stack[] = $openingBracket;
                }
            }

            $fixedJsonString .= $char;
        }

        return $fixedJsonString;
    }

    private function fixInvalidEscapes(string $jsonString): string
    {
        $pattern = '/(?<!\\\\)\\\\(?!["/\\\\bfnrt]|u[0-9a-fA-F]{4})/';
        return preg_replace($pattern, '', $jsonString);
    }

    private function fixMissingQuotes(string $jsonString): string
    {
        $pattern = '/([{,]\s*)([a-zA-Z0-9_\-]+?)\s*:/';
        return preg_replace($pattern, '$1"$2":', $jsonString);
    }

    private function fixTrailingCommas(string $jsonString): string
    {
        $pattern = '/,([\]\}])/';
        return preg_replace($pattern, '$1', $jsonString);
    }
}
