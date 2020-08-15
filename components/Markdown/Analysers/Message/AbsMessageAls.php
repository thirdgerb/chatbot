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
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsSendAls implements MessageAnalyser
{
    public function __invoke(
        string $content,
        array &$bufferedLines,
        Dialog $dialog
    ): ? Operator
    {
        $deliver = $dialog->send();
        if (!empty($bufferedLines)) {
            $text = implode(PHP_EOL, $bufferedLines);
            $deliver->message(MarkdownMsg::instance($text, $this->getLevel()));
        }
        $bufferedLines = [];

        $content = trim($content);
        if (!StringUtils::isEmptyStr($content)) {
            $level = $this->getLevel();
            $deliver->{$level}($content);
        }

        $deliver->over();
        return null;
    }

    abstract protected function getLevel() : string;

}