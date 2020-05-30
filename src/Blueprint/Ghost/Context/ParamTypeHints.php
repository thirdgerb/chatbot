<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Context;

use Commune\Support\Utils\TypeUtils;
use Illuminate\Support\Arr;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property string[] $a
 */
class ParamTypeHints
{
    /**
     * @var ParamParser[]
     */
    protected static $parsers = [];

    public static function registerParser(string $typeHint, ParamParser $parser) : void
    {
        self::$parsers[$typeHint] = $parser;
    }

    public static function validate(string $type, $value) : bool
    {
        $params = [];
        if (strpos($type, ',') !== false) {
            $typeDefines = explode(',', $type);
            $type = array_shift($typeDefines);
            $params = $typeDefines;
        }

        $isList = TypeUtils::isListTypeHint($type);
        if ($isList) {
            $type = TypeUtils::pureListTypeHint($type);
        }

        // 目标非数组会转化.
        if ($isList && !is_array($value)) {
            $value = Arr::wrap($value);
        }

        if (!$isList && is_array($value)) {
            $value = current($value);
        }

        if (isset(static::$parsers[$type])) {
            $parser = static::$parsers[$type];
            return $parser->validate($value, ...$params);
        }

        return $isList
            ? TypeUtils::listTypeHintValidate($type, $value)
            : TypeUtils::typeHintValidate($type, $value);

    }

    public static function parse(string $type, $value)
    {
        $params = [];
        if (strpos($type, ',') !== false) {
            $typeDefines = explode(',', $type);
            $type = array_shift($typeDefines);
            $params = $typeDefines;
        }

        $isList = TypeUtils::isListTypeHint($type);
        if ($isList) {
            $type = TypeUtils::pureListTypeHint($type);
        }

        // 目标非数组会转化.
        if ($isList && !is_array($value)) {
            $value = Arr::wrap($value);
        }

        if (!$isList && is_array($value)) {
            $value = current($value);
        }

        if (isset(static::$parsers[$type])) {
            $parser = static::$parsers[$type];
            return $parser->parse($value, ...$params);
        }

        return $isList
            ? TypeUtils::listTypeHintParse($type, $value)
            : TypeUtils::typeHintParse($type, $value);
    }

}