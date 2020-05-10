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

use Commune\Protocals\Host\ReactionMsg;
use Commune\Protocals\HostMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Support\Struct\Struct;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $id
 * @property-read string $level
 */
class IReaction extends AbsMessage implements ReactionMsg
{
    public function __construct(string $id, array $params, string $level = HostMsg::INFO)
    {
        $params['id'] = $id;
        $params['level'] = $level;

        parent::__construct($params);
    }


    public static function stub(): array
    {
        return [
            'id' => '',
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
        return StringUtils::normalizeString($this->id);
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function getReactionId(): string
    {
        return $this->id;
    }

    public function getParams(): array
    {
        $arr = $this->toArray();
        unset($arr['id']);
        unset($arr['level']);
        return $arr;
    }


}