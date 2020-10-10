<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Host\Convo\Media;

use Commune\Protocols\HostMsg;
use Commune\Protocols\HostMsg\Convo\Media\VideoMsg;
use Commune\Support\Message\AbsMessage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property string $resource
 * @property string $level
 * @property string $text
 */
class IVideoMsg extends AbsMessage implements VideoMsg
{

    public static function instance(
        string $resource,
        string $text = null,
        string $level = HostMsg::INFO
    ) : VideoMsg
    {
        return new static([
            'resource' => $resource,
            'text' => $text ?? $resource,
            'level' => $level
        ]);
    }

    public static function stub(): array
    {
        return [
            'resource' => '',
            'text' => '',
            'level' => HostMsg::INFO
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public function getProtocolId(): string
    {
        return $this->getResource();
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getResource(): string
    {
        return $this->resource;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function isEmpty(): bool
    {
        return empty($this->resource);
    }


}