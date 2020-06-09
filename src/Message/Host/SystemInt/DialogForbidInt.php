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
 */
class DialogForbidInt extends IIntentMsg
{
    const DEFAULT_LEVEL = HostMsg::INFO;
    const INTENT_NAME = HostMsg\DefaultIntents::SYSTEM_DIALOG_FORBID;

    public function __construct(string $contextName = '', string $policy = '')
    {
        parent::__construct(
            '',
            [
                'context' => $contextName,
                'policy' => $policy
            ]
        );
    }

    public static function intentStub(): array
    {
        return [
            'context' => '',
            'policy' => '',
        ];
    }

    public static function create(array $data = []): Struct
    {
        return new static(
            $data['context'] ?? '',
            $data['policy'] ?? ''
        );
    }
}