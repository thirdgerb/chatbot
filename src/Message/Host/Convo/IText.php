<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Host\Convo;

use Commune\Protocals\HostMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;
use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property string $text          文本正文
 * @property string $level         消息的级别.
 */
class IText extends AbsMessage implements VerbalMsg
{
    /**
     * @var string
     */
    protected $_text;

    public static function instance(string $text, string $level = HostMsg::INFO) : IText
    {
        return new static([
            'text' => $text,
            'level' => $level,
        ]);
    }

    public static function stub(): array
    {
        return [
            'text' => '',
            'level' => HostMsg::INFO,
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public function getProtocalId(): string
    {
        return $this->getText();
    }

    public function getText(): string
    {
        return $this->_text
            ?? $this->_text = trim($this->text);
    }


    public function getLevel(): string
    {
        return $this->level;
    }

    public function isEmpty(): bool
    {
        return StringUtils::isEmptyStr($this->_data['text']);
    }

    public function __toString()
    {
        return $this->getText();
    }
}