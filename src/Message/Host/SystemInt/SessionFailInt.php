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

use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Message\Host\IIntentMsg;
use Commune\Protocals\HostMsg;
use Commune\Support\Struct\Struct;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read int $errcode
 * @property-read string $errmsg
 */
class SessionFailInt extends IIntentMsg
{
    public function __construct(string $errmsg = null)
    {
        $errmsg = $errmsg
            ?? AppResponse::DEFAULT_ERROR_MESSAGES[AppResponse::HOST_SESSION_FAIL];

        parent::__construct(
            HostMsg\IntentMsg::SYSTEM_SESSION_FAIL,
            [
                'errmsg' => $errmsg
            ],
            HostMsg::ERROR
        );
    }

    public static function stub(): array
    {
        return [
            'intentName' => HostMsg\IntentMsg::SYSTEM_SESSION_FAIL,
            'errcode' => AppResponse::HOST_SESSION_FAIL,
            'errmsg' => AppResponse::DEFAULT_ERROR_MESSAGES[AppResponse::HOST_SESSION_FAIL],
            'level' => HostMsg::ERROR
        ];
    }

    public static function create(array $data = []): Struct
    {
        return new static($data['errmsg']);
    }

    public function getText(): string
    {
        return $this->errmsg;
    }
}