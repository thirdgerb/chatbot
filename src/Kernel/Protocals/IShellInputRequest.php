<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\Protocals;

use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\ShellInputResponse;
use Commune\Message\Intercom\IInputMsg;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Blueprint\Kernel\Protocals\ShellInputRequest;
use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $sessionId
 * @property-read bool $async
 * @property-read InputMsg $input
 */
class IShellInputRequest extends AbsMessage implements ShellInputRequest
{

    public static function instance(
        InputMsg $input,
        string $sessionId = '',
        bool $async = false
    ) : self
    {
        return new static([
            'sessionId' => $sessionId,
            'async' => $async,
            'input' => $input,
        ]);
    }

    public static function stub(): array
    {
        return [
            'sessionId' => '',
            'async' => false,
            'input' => [],
        ];
    }

    public static function relations(): array
    {
        return [
            'input' => IInputMsg::class
        ];
    }

    public function isEmpty(): bool
    {
        return false;
    }

    /*------- request -------*/

    public function getTraceId(): string
    {
        return $this->getInput()->getMessageId();
    }

    public function isAsync(): bool
    {
        return $this->async;
    }


    public function getSessionId(): string
    {
        $sessionId = $this->sessionId;
        return empty($sessionId)
            ? $this->getInput()->getSessionId()
            : $sessionId;
    }

    public function getInput(): InputMsg
    {
        return $this->input;
    }

    public function isInvalid(): ? string
    {
        return $this->getInput()->isInvalid();
    }

    public function response(
        int $errcode = AppResponse::SUCCESS,
        string $errmsg = ''
    ): ShellInputResponse
    {
        return new IShellInputResponse([
            'sessionId' => '',
            'async' => $this->isAsync(),
            'errcode' => $errcode,
            'errmsg' => $errmsg,
            'input' => $this->getInput(),
        ]);
    }


    /*------- protocal -------*/

    public function getProtocalId(): string
    {
        return StringUtils::namespaceSlashToDot(static::class);
    }

}