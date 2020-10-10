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
use Commune\Blueprint\Kernel\Protocols\AppResponse;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read int $errcode
 * @property-read string $errmsg
 */
class RequestFailInt extends IIntentMsg
{
    const DEFAULT_LEVEL = HostMsg::ERROR;
    const INTENT_NAME = HostMsg\DefaultIntents::SYSTEM_REQUEST_FAILURE;

    public static function instance(string $errmsg = null) : self
    {
        $data = [];
        if (isset($errmsg)) $data['errmsg'] = $errmsg;
        return new static($data);
    }

    public static function intentStub(): array
    {
        return [
            'errcode' => AppResponse::HOST_REQUEST_FAIL,
            'errmsg' => AppResponse::DEFAULT_ERROR_MESSAGES[AppResponse::HOST_REQUEST_FAIL],
        ];
    }

    public function getText(): string
    {
        return $this->errmsg;
    }

}