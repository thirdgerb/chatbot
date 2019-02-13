<?php

/**
 * Class TypeTransfer
 * @package Commune\Chatbot\Framework\Support
 */

namespace Commune\Chatbot\Framework\Support;


class TypeTransfer
{

    public static function toString($any) : string
    {
        if (is_string($any)) {
            return $any;
        } elseif (is_null($any)) {
            return 'null';
        } elseif (is_bool($any)) {
            return $any ? 'true' : 'false';
        } elseif (is_object($any) && !method_exists($any, '__toString')) {
            return serialize($any);
        }

        return strval($any);
    }

}