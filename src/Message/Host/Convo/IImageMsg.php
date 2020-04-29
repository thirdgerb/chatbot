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
use Commune\Protocals\Host\Convo\Media\ImageMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $resource
 */
class IImageMsg extends AbsMessage implements ImageMsg
{
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

    public function getNormalizedText(): string
    {
        return '';
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