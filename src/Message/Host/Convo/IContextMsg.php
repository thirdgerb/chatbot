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

use Commune\Protocals\Host\Convo\ContextMsg;
use Commune\Protocals\HostMsg;
use Commune\Support\Message\AbsMessage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property string $contextName       语境名称
 * @property string $contextId         语境Id
 * @property array $data               语境的数据.
 * @property string $level             语境的数据.
 */
class IContextMsg extends AbsMessage implements ContextMsg
{
    public static function stub(): array
    {
        return [
            'contextName' => '',
            'contextId' => '',
            'data' => [],
            'level' => HostMsg::INFO
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public function getNormalizedText(): string
    {
        return $this->toJson();
    }

    public function isEmpty(): bool
    {
        return false;
    }


}