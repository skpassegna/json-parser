<?php

namespace Skpassegna\JsonParser;

use Skpassegna\JsonParser\Exceptions\JsonEncryptionException;
use Skpassegna\JsonParser\Exceptions\JsonCompressionException;
use Skpassegna\JsonParser\Contracts\JsonIterable;
use Skpassegna\JsonParser\Contracts\JsonAccessible;

class JsonObject implements JsonAccessible, JsonIterable
{
    /**
     * @var array The underlying data for the JSON object.
     */
    protected $data;

    /**
     * JsonObject constructor.
     *
     * @param array $data The data for the JSON object.
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Get a property value by key.
     *
     * @param string $key The property key.
     * @return mixed The property value.
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    /**
     * Set a property value by key.
     *
     * @param string $key The property key.
     * @param mixed $value The property value.
     */
    public function __set(string $key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Check if a property exists by key.
     *
     * @param string $key The property key.
     * @return bool True if the property exists, false otherwise.
     */
    public function __isset(string $key): bool
    {
        return $this->has($key);
    }

    /**
     * Unset a property by key.
     *
     * @param string $key The property key.
     */
    public function __unset(string $key)
    {
        $this->remove($key);
    }

    /**
     * Get a property value by key.
     *
     * @param string $key The property key.
     * @param mixed $default The default value to return if the property doesn't exist.
     * @return mixed The property value, or the default value if the property doesn't exist.
     */
    public function get(string $key, $default = null)
    {
        if (str_contains($key, '.')) {
            return $this->getNestedValue($key, $default);
        }

        return $this->data[$key] ?? $default;
    }

    /**
     * Set a property value by key.
     *
     * @param string $key The property key.
     * @param mixed $value The property value.
     * @return $this
     */
    public function set(string $key, $value): self
    {
        if (str_contains($key, '.')) {
            $this->setNestedValue($key, $value);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Check if a property exists by key.
     *
     * @param string $key The property key.
     * @return bool True if the property exists, false otherwise.
     */
    public function has(string $key): bool
    {
        if (str_contains($key, '.')) {
            return $this->hasNestedValue($key);
        }

        return array_key_exists($key, $this->data);
    }

    /**
     * Remove a property by key.
     *
     * @param string $key The property key.
     * @return $this
     */
    public function remove(string $key): self
    {
        if (str_contains($key, '.')) {
            $this->removeNestedValue($key);
        } else {
            unset($this->data[$key]);
        }

        return $this;
    }

    /**
     * Merge this object with another JSON object.
     *
     * @param JsonObject $other The other JSON object to merge.
     * @return JsonObject The merged JSON object.
     */
    public function merge(JsonObject $other): JsonObject
    {
        $merged = clone $this;
        foreach ($other as $key => $value) {
            if ($merged->has($key)) {
                $existingValue = $merged->get($key);
                if ($existingValue instanceof JsonObject && $value instanceof JsonObject) {
                    $merged->set($key, $existingValue->merge($value));
                } elseif ($existingValue instanceof JsonArray && $value instanceof JsonArray) {
                    $merged->set($key, $existingValue->merge($value));
                } else {
                    $merged->set($key, $value);
                }
            } else {
                $merged->set($key, $value);
            }
        }
        return $merged;
    }

    /**
     * Check if this object is equal to another JSON object.
     *
     * @param JsonObject $other The other JSON object to compare.
     * @return bool True if the objects are equal, false otherwise.
     */
    public function equals(JsonObject $other): bool
    {
        if (count($this->data) !== count($other->data)) {
            return false;
        }

        foreach ($this->data as $key => $value) {
            if (!$other->has($key)) {
                return false;
            }

            $otherValue = $other->get($key);
            if ($value instanceof JsonObject && $otherValue instanceof JsonObject) {
                if (!$value->equals($otherValue)) {
                    return false;
                }
            } elseif ($value instanceof JsonArray && $otherValue instanceof JsonArray) {
                if (!$value->equals($otherValue)) {
                    return false;
                }
            } else {
                if ($value !== $otherValue) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Transform this object by applying a callback function to each value.
     *
     * @param callable $callback The callback function to apply.
     * @return JsonObject The transformed JSON object.
     */
    public function transform(callable $callback): JsonObject
    {
        $transformed = new JsonObject();
        foreach ($this->data as $key => $value) {
            if ($value instanceof JsonObject) {
                $transformed->set($key, $value->transform($callback));
            } elseif ($value instanceof JsonArray) {
                $transformed->set($key, $value->transform($callback));
            } else {
                $transformed->set($key, $callback($value));
            }
        }
        return $transformed;
    }

    /**
     * Get all properties as an array.
     *
     * @return array The properties array.
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Convert the object to a JSON string.
     *
     * @return string The JSON string representation of the object.
     */
    public function toJson(): string
    {
        return json_encode($this->data);
    }

    /**
     * Get an iterator for the object properties.
     *
     * @return \Traversable An iterator for the object properties.
     */
    public function getIterator(): \Traversable
    {
        return new JsonObjectIterator($this->data);
    }

    /**
     * Check if the offset exists in the object.
     *
     * @param mixed $offset The offset to check.
     * @return bool True if the offset exists, false otherwise.
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * Get the value at the specified offset.
     *
     * @param mixed $offset The offset to retrieve.
     * @return mixed The value at the specified offset.
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Set the value at the specified offset.
     *
     * @param mixed $offset The offset to set.
     * @param mixed $value The value to set.
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * Unset the value at the specified offset.
     *
     * @param mixed $offset The offset to unset.
     */
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    /**
     * Get a nested value by key path.
     *
     * @param string $keyPath The key path (e.g., 'parent.child.grandchild').
     * @param mixed $default The default value to return if the key path doesn't exist.
     * @return mixed The nested value, or the default value if the key path doesn't exist.
     */
    private function getNestedValue(string $keyPath, $default = null)
    {
        $keys = explode('.', $keyPath);
        $value = $this->data;

        foreach ($keys as $key) {
            if (!is_array($value) && !($value instanceof JsonObject)) {
                return $default;
            }

            if (is_array($value)) {
                $value = $value[$key] ?? null;
            } else {
                $value = $value->get($key);
            }
        }

        return $value ?? $default;
    }

    /**
     * Set a nested value by key path.
     *
     * @param string $keyPath The key path (e.g., 'parent.child.grandchild').
     * @param mixed $value The value to set.
     */
    private function setNestedValue(string $keyPath, $value): void
    {
        $keys = explode('.', $keyPath);
        $lastKey = array_pop($keys);
        $ref = &$this->data;

        foreach ($keys as $key) {
            if (!isset($ref[$key]) || !is_array($ref[$key])) {
                $ref[$key] = [];
            }

            $ref = &$ref[$key];
        }

        $ref[$lastKey] = $value;
    }

    /**
     * Check if a nested value exists by key path.
     *
     * @param string $keyPath The key path (e.g., 'parent.child.grandchild').
     * @return bool True if the nested value exists, false otherwise.
     */
    private function hasNestedValue(string $keyPath): bool
    {
        $keys = explode('.', $keyPath);
        $value = $this->data;

        foreach ($keys as $key) {
            if (!is_array($value) && !($value instanceof JsonObject)) {
                return false;
            }

            if (is_array($value)) {
                if (!array_key_exists($key, $value)) {
                    return false;
                }

                $value = $value[$key];
            } else {
                if (!$value->has($key)) {
                    return false;
                }

                $value = $value->get($key);
            }
        }

        return true;
    }

    /**
     * Remove a nested value by key path.
     *
     * @param string $keyPath The key path (e.g., 'parent.child.grandchild').
     */
    private function removeNestedValue(string $keyPath): void
    {
        $keys = explode('.', $keyPath);
        $lastKey = array_pop($keys);
        $ref = &$this->data;

        foreach ($keys as $key) {
            if (!isset($ref[$key]) || !is_array($ref[$key])) {
                return;
            }

            $ref = &$ref[$key];
        }

        unset($ref[$lastKey]);
    }

    /**
     * Compress the JSON object.
     *
     * @return string The compressed JSON string.
     * @throws JsonCompressionException If an error occurs during compression.
     */
    public function compress(): string
    {
        $jsonString = $this->toJson();

        $compressedData = gzcompress($jsonString, 9); // Adjust compression level as needed

        if ($compressedData === false) {
            throw new JsonCompressionException('Failed to compress JSON data.');
        }

        return $compressedData;
    }

    /**
     * Decompress a compressed JSON string.
     *
     * @param string $compressedJson The compressed JSON string.
     * @return JsonObject The decompressed JSON object.
     * @throws JsonCompressionException If an error occurs during decompression.
     */
    public static function decompress(string $compressedJson): JsonObject
    {
        $decompressedData = @gzuncompress($compressedJson);

        if ($decompressedData === false) {
            throw new JsonCompressionException('Failed to decompress JSON data.');
        }

        $jsonParser = new JsonParser();
        return $jsonParser->parse($decompressedData);
    }

    /**
     * Encrypt the JSON object.
     *
     * @param string $key The encryption key.
     * @return string The encrypted JSON string.
     * @throws JsonEncryptionException If an error occurs during encryption.
     */
    public function encrypt(string $key): string
    {
        $jsonString = $this->toJson();

        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($ivLength);

        $encryptedData = openssl_encrypt($jsonString, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        if ($encryptedData === false) {
            throw new JsonEncryptionException('Failed to encrypt JSON data.');
        }

        return base64_encode($iv . $encryptedData);
    }

    /**
     * Decrypt an encrypted JSON string.
     *
     * @param string $encryptedJson The encrypted JSON string.
     * @param string $key The encryption key.
     * @return JsonObject The decrypted JSON object.
     * @throws JsonEncryptionException If an error occurs during decryption.
     */
    public static function decrypt(string $encryptedJson, string $key): JsonObject
    {
        $data = base64_decode($encryptedJson);

        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($data, 0, $ivLength);
        $encryptedData = substr($data, $ivLength);

        $decryptedData = openssl_decrypt($encryptedData, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        if ($decryptedData === false) {
            throw new JsonEncryptionException('Failed to decrypt JSON data.');
        }

        $jsonParser = new JsonParser();
        return $jsonParser->parse($decryptedData);
    }

    /**
     * Format the JSON object with proper indentation and spacing.
     *
     * @param int $options JSON formatting options (e.g., JSON_PRETTY_PRINT).
     * @return string The formatted JSON string.
     */
    public function format(int $options = JSON_PRETTY_PRINT): string
    {
        return json_encode($this->data, $options);
    }

    /**
     * Minify the JSON object by removing unnecessary whitespace and formatting.
     *
     * @return string The minified JSON string.
     */
    public function minify(): string
    {
        return json_encode($this->data);
    }
}
