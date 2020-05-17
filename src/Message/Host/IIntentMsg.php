<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Host;

use Commune\Protocals\HostMsg\IntentMsg;
use Commune\Protocals\HostMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Support\Struct\Struct;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $intentName
 * @property-read string $level
 */
class IIntentMsg extends AbsMessage implements IntentMsg
{
    public function __construct(string $intentName, array $params, string $level = HostMsg::INFO)
    {
        $params['intentName'] = $intentName;
        $params['level'] = $level;

        parent::__construct($params);
    }


    public static function stub(): array
    {
        return [
            'intentName' => '',
            'level' => HostMsg::INFO
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public static function create(array $data = []): Struct
    {
        return new static(
            $data['id'] ?? '',
            $data,
            $data['level'] ?? HostMsg::INFO
        );
    }

    public function isBroadcasting(): bool
    {
        return true;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getNormalizedText(): string
    {
        return StringUtils::normalizeString($this->intentNamed);
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function getIntentName(): string
    {
        return $this->intentName;
    }

    public function getSlots(): array
    {
        $arr = $this->toArray();
        unset($arr['intentName']);
        unset($arr['level']);
        return $arr;
    }


}