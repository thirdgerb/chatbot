<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Markdown;

use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class MarkdownUtils
{
    // 标准方式.
    //  [//]: # (@comment content)
    const COMMENT_PATTERN = '/^\[\/\/\]:\s*#\s+\(\s*@([a-zA-Z0-9-_]+)(\s+.*){0,1}\)$/';
    const TITLE_PATTERN = '/^(#+)\s+(.+)$/';


    public static function parseSingleLine(string $line, string $method = null) : string
    {
        $line = trim($line);
        if (!self::isSingleLine($line)) {
            throw new \InvalidArgumentException(
                ($method ?? __METHOD__)
                . ' only accept single line'
            );
        }
        return $line;
    }

    public static function createCommentLine(string $comment, string $content = '') : string
    {
        return "[//]: # (@$comment $content)";
    }

    public static function isSingleLine(string $line) : bool
    {
        return mb_strpos($line, "\n") === false;
    }

    public static function quote(string $text) : string
    {
        $lines = explode("\n", $text);
        $output = implode("\n    ", $lines);
        return "    $output";
    }

    public static function code(string $text, string $codeType = null) : string
    {
        $codeType = $codeType ?? '';
        return "```$codeType\n$text\n```";
    }


    public static function isDividerLine(string $last, string $current) : bool
    {
        $last = trim($last);
        $current = trim($current);

        return StringUtils::isEmptyStr($last)
            && (bool) preg_match('/^----+$/', $current);
    }


    public static function parseCommentLine(string $line) : ? array
    {
        $line = self::parseSingleLine($line);
        preg_match(self::COMMENT_PATTERN, $line, $matches);

        if (empty($matches)) {
            return null;
        }
        $comment = trim($matches[1]);
        $content = trim($matches[2] ?? '');

        if (empty($comment)) {
            return null;
        }

        return [
            $comment,
            $content
        ];
    }

    public static function parseTitle(string $line) : ? array
    {
        $line = self::parseSingleLine($line, __METHOD__);
        preg_match(self::TITLE_PATTERN, $line, $matches);

        if (empty($matches)) {
            return null;
        }

        return [
            $level = strlen($matches[1]),
            $title = trim($matches[2]),
        ];
    }

    public static function maybeTitleUnderline(string $line) : int
    {
        $line = trim($line);
        if (
            (bool) preg_match('/^-+$/', $line)
            || (bool) preg_match('/^=+$/', $line)
        )  {
            return strlen($line);
        }

        return 0;
    }
}