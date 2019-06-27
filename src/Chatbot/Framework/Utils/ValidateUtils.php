<?php


namespace Commune\Chatbot\Framework\Utils;


class ValidateUtils
{
    public static function getTypeOf($value) : string
    {
        return is_object($value) ? get_class($value) : gettype($value);
    }

}