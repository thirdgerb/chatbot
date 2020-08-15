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
use Commune\Blueprint\Ghost\Tools\Deliver;
use Commune\Components\Markdown\Analysers\MessageAnalyser;
use Commune\Message\Host\Convo\Verbal\MarkdownMsg;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsMessageAls implements MessageAnalyser
{
    public function __invoke(
        string $content,
        array &$bufferedLines,
        Dialog $dialog
    ): ? Operator
    {
        // 将 buffer 的 message 先发送.
        $deliver = $dialog->send();
        if (!empty($bufferedLines)) {
            $text = implode(PHP_EOL, $bufferedLines);
            $deliver->message(MarkdownMsg::instance($text, $this->getLevel()));
        }
        $bufferedLines = [];

        $this->deliverCommentContent($deliver, $content);
        $deliver->over();
        return null;
    }

    protected function deliverCommentContent(Deliver $deliver, string $content) : void
    {
        $content = trim($content);
        if (!StringUtils::isEmptyStr($content)) {
            $level = $this->getLevel();
            $deliver->{$level}($content);

        }
    }

    abstract protected function getLevel() : string;

}