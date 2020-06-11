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
use Commune\Protocals\HostMsg;
use Commune\Support\Struct\Struct;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $await     等待的语境
 * @property-read string $matched   命中的意图
 */
class DialogConfuseInt extends IIntentMsg
{
    const DEFAULT_LEVEL = HostMsg::NOTICE;
    const INTENT_NAME = HostMsg\DefaultIntents::SYSTEM_DIALOG_CONFUSE;


    public function __construct(string $await = '', string $matchedIntent = '')
    {
        parent::__construct('', ['await' => $await, 'matched' => $matchedIntent]);
    }

    public static function intentStub(): array
    {
        return ['await' => '', 'matched' => ''];
    }

    public static function create(array $data = []): Struct
    {
        return new static($data['await'] ?? '');
    }

}