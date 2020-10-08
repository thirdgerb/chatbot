<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Host\Convo\Verbal;

use Commune\Message\Host\Convo\IText;
use Commune\Protocals\HostMsg\Tags\Markdown;
use Commune\Support\Markdown\MarkdownUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class MarkdownMsg extends IText implements Markdown
{

    /**
     * @param string $text
     * @return static
     */
    public static function quote(string $text) : IText
    {
        return static::instance(MarkdownUtils::quote($text));
    }

    public static function code(string $text, string $type = 'shell') : IText
    {
        return static::instance(MarkdownUtils::code($text, $type));
    }

    public function toMarkdownText(): string
    {
        return $this->getText();
    }


}