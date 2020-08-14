<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Markdown\Analysers\Message;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Components\Markdown\Analysers\MessageAnalyser;
use Commune\Message\Host\Convo\Verbal\MarkdownMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class BreakAls implements MessageAnalyser
{
    public function __invoke(
        string $content,
        array &$bufferedLines,
        Dialog $dialog
    ): ? Operator
    {
        if (!empty($bufferedLines)) {
            $text = implode(PHP_EOL, $bufferedLines);
            $dialog->send()->message(MarkdownMsg::instance($text));
        }
        $bufferedLines = [];
        return null;
    }


}