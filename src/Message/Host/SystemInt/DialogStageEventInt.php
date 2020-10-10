<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Host\SystemInt;


use Commune\Message\Host\IIntentMsg;
use Commune\Protocols\HostMsg;

/**
 * 用 event + stage 的方式来定义输出结果.
 * 通常到 translator 中寻找响应文本.
 *
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $event     事件
 * @property-read string $stage     所处的 stage fullname
 * @property-read string[] $slots
 */
class DialogStageEventInt  extends IIntentMsg
{
    const DEFAULT_LEVEL = HostMsg::INFO;
    const INTENT_NAME = HostMsg\DefaultIntents::SYSTEM_DIALOG_STAGE_EVENT;


    const ENTITY_EVENT = 'event';
    const ENTITY_STAGE = 'stage';
    const ENTITY_SLOTS = 'slots';

    public static function instance(
        string $event,
        string $stage,
        array $slots = []
    )
    {
        return new static(get_defined_vars());
    }

    public static function intentStub(): array
    {
        return [
            self::ENTITY_EVENT => '',
            self::ENTITY_STAGE => '',
            self::ENTITY_SLOTS => [],
        ];
    }

    public function getSlots(): array
    {
        return $this->slots;
    }
}
