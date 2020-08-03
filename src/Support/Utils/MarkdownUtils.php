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

    public static function quote(string $text) : string
    {
        $lines = explode("\n", $text);
        $output = implode("\n    ", $lines);
        return "    $output";
    }

    public static function code(string $text, string $codeType = null) : string
    {
        $codeType = $codeType ?? '';
        $text = static::quote($text);
        return "\n```$codeType\n\n$text\n```";
    }
}