<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Utils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class MarkdownUtils
{
    const COMMENT_PATTERN = '/^\s*\[(.*)\]:(.*)$/';

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
        $line = trim($line);
        if (mb_strpos($line, "\n")) {
            throw new \InvalidArgumentException(
                __METHOD__
                . ' only accept one line'
            );
        }

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