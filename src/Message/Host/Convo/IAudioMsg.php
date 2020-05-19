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
use Commune\Protocals\HostMsg\Convo\Media\AudioMsg;
use Commune\Support\Struct\Struct;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $resource
 */
class IAudioMsg extends AbsMessage implements AudioMsg
{

    public function __construct(string $resource)
    {
        parent::__construct(['resource' => $resource]);
    }

    public static function stub(): array
    {
        return [
            'resource' => '',
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public static function create(array $data = []): Struct
    {
        return new static($data['resource'] ?? '');
    }

    public function getText(): string
    {
        return $this->resource;
    }

    public function isBroadcasting(): bool
    {
        return true;
    }

    public function getResource(): string
    {
        return $this->resource;
    }


    public function isEmpty(): bool
    {
        return empty($this->_data['resource']);
    }

    public function getLevel(): string
    {
        return HostMsg::INFO;
    }
}