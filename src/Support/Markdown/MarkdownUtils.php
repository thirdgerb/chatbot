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


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class MarkdownUtils
{
    const COMMENT_PATTERN = '/^\[(.*)\]:(.*)$/';


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

    public static function parseCommentLine(string $line) : ? array
    {
        $line = self::parseSingleLine($line, __METHOD__);
        preg_match(self::COMMENT_PATTERN, $line, $matches);
        if (empty($matches)) {
            return null;
        }

        return [
            $comment = trim($matches[1]),
            $content = trim($matches[2]),
        ];
    }
}