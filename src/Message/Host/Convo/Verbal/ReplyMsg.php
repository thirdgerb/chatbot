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

use Commune\Protocols\HostMsg;
use Commune\Protocols\HostMsg\Convo\VerbalMsg;
use Commune\Support\Message\AbsMessage;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property string $quote
 * @property string $reply
 * @property string $level
 */
class ReplyMsg extends AbsMessage implements VerbalMsg
{

    public static function instance(
        string $quote,
        string $reply,
        string $level = HostMsg::INFO
    ) : ReplyMsg
    {
        return new static(get_defined_vars());
    }

    public static function stub(): array
    {
        return [
            'quote' => '',
            'reply' => '',
            'level' => HostMsg::INFO
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public function getProtocolId(): string
    {
        return $this->reply;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function isEmpty(): bool
    {
        return empty($this->reply);
    }

    public function getText(): string
    {
        $reply = $this->reply;
        $quote = $this->quote;
        return "$quote\n----\n$reply";
    }


}