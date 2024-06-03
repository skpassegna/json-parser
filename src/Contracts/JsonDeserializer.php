<?php

namespace Skpassegna\JsonParser\Contracts;

interface JsonDeserializer
{
    /**
     * Deserialize a JSON value.
     *
     * @param string $json The JSON value to deserialize.
     * @return mixed The deserialized value.
     */
    public function deserialize(string $json);
}