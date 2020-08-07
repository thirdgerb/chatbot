<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Support;

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Ghost\Context;
use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ContextUtils
{

    public static function makeFullStageName(string $contextName, string $stageName) : string
    {
        return StringUtils::gluePrefixAndName(
            $contextName,
            $stageName,
            Context::CONTEXT_STAGE_DELIMITER
        );
    }

    public static function divideContextNameFromStageName(string $stageFullname) : array
    {
        $exploded = explode(Context::CONTEXT_STAGE_DELIMITER, $stageFullname);
        $stageName = array_pop($exploded);
        return [implode(Context::CONTEXT_STAGE_DELIMITER, $exploded), $stageName];
    }

    public static function parseShortStageName(string $stageFullName, string $contextName) : string
    {
        $length = strlen($contextName);
        $first = substr($stageFullName, 0, $length);
        $last = substr($stageFullName, $length);

        if ($first !== $contextName) {
            throw new InvalidArgumentException(
                "stage full name must start with its context name"
            );
        }

        return trim($last, Context::CONTEXT_STAGE_DELIMITER);
    }

    public static function normalizeContextName(string $contextName) : string
    {
        return StringUtils::normalizeString(StringUtils::namespaceSlashToDot($contextName));
    }

    public static function normalizeStageName(string $stageName) : string
    {
        return strtolower($stageName);
    }

    public static function normalizeIntentName(string $intentName) : string
    {
        return static::normalizeContextName($intentName);
    }

    public static function isValidContextName(string $str) : bool
    {
        $pattern = '/^[a-z][a-z0-9]*(\.[a-z][a-z0-9]*)*$/';
        return (bool) preg_match($pattern, $str);
    }

    public static function isValidMemoryName(string $str) : bool
    {
        return self::isValidContextName($str);
    }

    public static function normalizeMemoryName(string $str) : string
    {
        return strtolower(StringUtils::namespaceSlashToDot($str));
    }

    public static function isValidStageFullName(string $str) : bool
    {
        return self::isValidIntentName($str);
    }

    public static function isValidStageName(string $name) : bool
    {
        // 允许为空.
        if ($name === '') {
            return true;
        }
        $pattern = '/^[a-z][a-z_0-9]+$/';
        return (bool) preg_match($pattern, $name);
    }

    public static function isValidIntentName(string $str) : bool
    {
        $pattern = '/^[a-z][a-z0-9]*(\.[a-z][a-z0-9]*)*(\.[a-z][a-z_0-9]*){0,1}$/';
        return (bool) preg_match($pattern, $str);
    }

    public static function isValidEntityName(string $str) : bool
    {
        return true;
    }


    public static function isWildcardIntentPattern(string $pattern) : bool
    {
        return StringUtils::isWildcardPattern($pattern);
    }

    public static function wildcardIntentMatch(string $wildcardId, string $actual) : bool
    {
        return StringUtils::wildcardMatch($wildcardId, $actual, '\w+');
    }

    public static function wildcardIntentSearch(string $wildcardId, array $searches) : array
    {
        return StringUtils::wildcardSearch($wildcardId, $searches, '\w+');
    }

}