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
 * @property-read string $attr
 */
class DialogRequireInt extends IIntentMsg
{
    const DEFAULT_LEVEL = HostMsg::INFO;
    const INTENT_NAME = HostMsg\DefaultIntents::SYSTEM_DIALOG_REQUIRE;


    public function __construct(string $attr = '')
    {
        parent::__construct(
            '',
            [
                'attr' => $attr
            ]
        );
    }

    public static function intentStub(): array
    {
        return [
            'attr' => '',
        ];
    }

    public static function create(array $data = []): Struct
    {
        return new static($data['attr'] ?? '');
    }

}