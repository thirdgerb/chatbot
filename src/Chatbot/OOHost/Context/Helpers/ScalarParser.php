<?php


namespace Commune\Chatbot\OOHost\Context\Helpers;


use Commune\Chatbot\Framework\Utils\StringUtils;

class ScalarParser
{
    public static function toString($value) : string
    {
        if (is_array($value)) {
            $value = current($value);
        }
        return strval($value);
    }

    public static function toInt($value) : int
    {
        if (is_array($value)) {
            $value = current($value);
        }
        return intval($value);
    }

    public static function toFloat($value) : float
    {
        if (is_array($value)) {
            $value = current($value);
        }
        return floatval($value);
    }

    public static function toBool($value) : bool
    {
        if (is_array($value)) {
            $value = current($value);
        }
        return boolval($value);
    }


    public static function toStringArr($value) : array
    {
        if (!is_array($value)) {
            $value = [$value];
        }
        return array_map(
            function($i) {
                return strval($i);
            },
            array_filter($value, function($i){
                return is_scalar($i) || StringUtils::couldBeString($i);
            })
        );
    }


    public static function toIntArr($value) : array
    {
        if (!is_array($value)) {
            $value = [$value];
        }
        return array_map(
            function($i) {
                return intval($i);
            },
            array_filter($value, function($i){
                return is_scalar($i);
            })
        );
    }


    public static function toFloatArr($value) : array
    {
        if (!is_array($value)) {
            $value = [$value];
        }
        return array_map(
            function($i) {
                return floatval($i);
            },
            array_filter($value, function($i){
                return is_scalar($i);
            })
        );
    }


    public static function toBoolArr($value) : array
    {
        if (!is_array($value)) {
            $value = [$value];
        }
        return array_map(
            function($i) {
                return boolval($i);
            },
            array_filter($value, function($i){
                return is_scalar($i);
            })
        );
    }


}