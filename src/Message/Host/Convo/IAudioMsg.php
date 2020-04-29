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
use Commune\Protocals\Host\Convo\Media\AudioMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $resource
 * @property-read string $level
 */
class IAudioMsg extends AbsMessage implements AudioMsg
{
    public static function stub(): array
    {
        return [
            'resource' => '',
            'level' => HostMsg::INFO
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


}