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

use Commune\Support\Regex\Patterns;
use Commune\Support\Utils\StringUtils;


/**
 * 用于chatbot 命令管理的函数.
 */
class CommandUtils
{
    // 命令的长度
    const VALID_COMMAND_LEN = 30;

    const PRETTY_JSON = JSON_UNESCAPED_UNICODE
    | JSON_UNESCAPED_SLASHES
    | JSON_PRETTY_PRINT;

    protected static $userCommandMark = '#';

    protected static $validCommandLen = 30;

    /**
     * @param string $mark
     */
    public static function setUserCommandMark(string $mark) : void
    {
        self::$userCommandMark = $mark;
    }

    /**
     * 修改合法命令限定的长度.
     *
     * @param int $length
     */
    public static function setValidCommandLen(int $length) : void
    {
        // 永远不要相信程序员会干什么蠢事...
        if ($length > 0) {
            self::$validCommandLen = $length;
        }
    }

    /**
     * 获取一个没有mark 的command str, 通常用于解析命令参数.
     * 这是因为定义一个命令时, 名字是固定的, 但标记为命令的command mark 可能不一样.
     *
     * 比如:
     *
     * /help
     * #help
     * .help
     *
     * 不应该在定义命令的时候把这个限制死了.
     *
     * @param string $text
     * @param string $commandMark
     * @return null|string
     */
    public static function getCommandStr(string $text, string $commandMark = null) : ? string
    {
        $commandMark = $commandMark ?? self::$userCommandMark;

        $hasMark = strlen($commandMark) === 0
            || mb_strpos($text, $commandMark) === 0;

        // 没有mark 的一定不是字符串.
        if (!$hasMark) {
            return null;
        }

        if (
            mb_strpos($text, $commandMark) === 0
            && 0 !== mb_strpos($text, "$commandMark$commandMark")
        ) {
            $markLen = mb_strlen($commandMark);
            $text = mb_substr($text, $markLen);
        }

        return StringUtils::sbc2dbc($text);
    }


    /**
     * 这个方法可用用来判断, 一个字符串是否是一个合法的命令.
     * 如果返回值是有命令的 name, 则是合法的. 如果返回null, 说明不是一个命令字符串.
     *
     * @param string $commandStr
     * @return null|string
     */
    public static function getCommandNameStr(string $commandStr) : ? string
    {
        // 决定终点
        $spacePos = mb_strpos($commandStr, ' ');
        if ($spacePos !== false && $spacePos > 0) {
            $commandStr = mb_substr($commandStr, 0, $spacePos);
        }

        // 判断
        if (self::validateCommandName($commandStr)) {
            return $commandStr;
        }

        return null;
    }

    /**
     * 判断一个字符串是否匹配一个命令名称.
     *
     * @param string $text
     * @param string $commandName
     * @return bool
     */
    public static function matchCommandName(string $text, string $commandName) : bool
    {
        return !empty($text)
            && (
                $text === $commandName
                || strpos($text, $commandName. ' ') === 0
            );
    }

    /**
     * 判断命令名是否合法.
     *
     * @param string $suggestCommand
     * @return bool
     */
    public static function validateCommandName(string $suggestCommand) : bool
    {
        // 只允许中文 + 英文 + 数字 作为命令.
        $validChars = preg_match(
            $p = '/^'. Patterns::CH_EN_NUM_CHAR .'+$/u',
            $suggestCommand
        );

        return $validChars
            && mb_strlen($suggestCommand) < self::VALID_COMMAND_LEN;
    }

}