<?php

namespace Skpassegna\JsonParser;

class JsonValue
{
    /**
     * @var mixed The underlying value.
     */
    protected $value;

    /**
     * JsonValue constructor.
     *
     * @param mixed $value The value to wrap.
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Get the underlying value.
     *
     * @return mixed The underlying value.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Convert the value to a string.
     *
     * @return string The string representation of the value.
     */
    public function __toString(): string
    {
        return (string)$this->value;
    }

    /**
     * Check if the value is a boolean.
     *
     * @return bool True if the value is a boolean, false otherwise.
     */
    public function isBoolean(): bool
    {
        return is_bool($this->value);
    }

    /**
     * Check if the value is a number.
     *
     * @return bool True if the value is a number, false otherwise.
     */
    public function isNumber(): bool
    {
        return is_numeric($this->value);
    }

    /**
     * Check if the value is an integer.
     *
     * @return bool True if the value is an integer, false otherwise.
     */
    public function isInteger(): bool
    {
        return is_int($this->value);
    }

    /**
     * Check if the value is a float.
     *
     * @return bool True if the value is a float, false otherwise.
     */
    public function isFloat(): bool
    {
        return is_float($this->value);
    }

    /**
     * Check if the value is a string.
     *
     * @return bool True if the value is a string, false otherwise.
     */
    public function isString(): bool
    {
        return is_string($this->value);
    }

    /**
     * Check if the value is null.
     *
     * @return bool True if the value is null, false otherwise.
     */
    public function isNull(): bool
    {
        return is_null($this->value);
    }
}
