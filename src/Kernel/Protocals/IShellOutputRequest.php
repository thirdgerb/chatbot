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
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;
use Commune\Protocals\IntercomMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $sessionId
 * @property-read string $traceId
 * @property-read bool $async
 * @property-read bool $stateless
 *
 * @property-read IntercomMsg[] $outputs
 */
class IShellOutputRequest extends AbsMessage implements ShellOutputRequest
{
    public static function stub(): array
    {
        return [
            'sessionId' => '',
            'traceId' => '',
            'async' => false,
            'stateless' => false,
            'outputs' => [],
        ];
    }

    public static function relations(): array
    {
        return [
            'outputs[]' => IntercomMsg::class,
        ];
    }

    public function isInvalid(): ? string
    {
        return null;
    }


    public function isEmpty(): bool
    {
        return false;
    }

    /*------ protocal ------*/

    public function getTraceId(): string
    {
        return $this->traceId;
    }

    public function getProtocalId(): string
    {
        return StringUtils::namespaceSlashToDot(static::class);
    }

    public function __set_outputs(string $name, array $outputs)  :void
    {
        $this->_data[$name] = array_filter($outputs, function($message) {
            return $message instanceof IntercomMsg;
        });
    }

    /*------ request ------*/

    public function isStateless(): bool
    {
        return $this->stateless;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function isAsync(): bool
    {
        return $this->async;
    }

    public function getOutputs(): array
    {
        return $this->outputs;
    }

    public function setOutputs(array $messages): void
    {
        $this->outputs = $messages;
    }

    public function response(
        int $errcode = AppResponse::SUCCESS,
        string $errmsg = ''
    ): ShellOutputResponse
    {
        return new IShellOutputResponse([
            'sessionId' => $this->getSessionId(),
            'traceId' => $this->getTraceId(),
            'errcode' => $errcode,
            'errmsg' => $errmsg,
            'outputs' => $this->getOutputs()
        ]);
    }


}