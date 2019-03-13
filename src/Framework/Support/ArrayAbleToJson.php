<?php

/**
 * Class Jsonable
 * @package Commune\Chatbot\Framework\Support
 */

namespace Commune\Chatbot\Framework\Support;


trait ArrayAbleToJson
{

    public function toJson(int $option = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) : string
    {
        $option = $option ?? JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;
        return json_encode($this->toArray(), $option);
    }

    public function toString() : string
    {
        return $this->toJson();
    }

    public function jsonSerialize()
    {
        return $this->toJson();
    }

    public function __toString()
    {
        return $this->toString();
    }


}