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
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Message\Intercom\IInputMsg;
use Commune\Protocals\HostMsg;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $sessionId
 * @property-read bool $async
 * @property-read InputMsg $input
 */
class IGhostRequest extends AbsMessage implements GhostRequest
{
    public static function stub(): array
    {
        return [
            'sessionId' => '',
            'async' => false,
            'input' => []
        ];
    }

    public static function relations(): array
    {
        return [
            'input' => IInputMsg::class
        ];
    }

    /*-------- message --------*/

    public function isInvalid(): ? string
    {
        return $this->getInput()->isInvalid();
    }


    public function isEmpty(): bool
    {
        return false;
    }

    /*-------- protocal --------*/

    public function getProtocalId(): string
    {
        return StringUtils::namespaceSlashToDot(static::class);
    }

    /*-------- protocal --------*/

    public function getTraceId(): string
    {
        return $this->getInput()->getMessageId();
    }

    public function getSessionId(): string
    {
        $sessionId = $this->sessionId;
        return empty($sessionId)
            ? $this->getInput()->getSessionId()
            : $sessionId;
    }

    public function isAsync(): bool
    {
        return $this->async;
    }

    public function isStateless(): bool
    {
        return $this->getInput()->getMessage() instanceof InputMsg;
    }

    public function response(int $errcode = AppResponse::SUCCESS, string $errmsg = ''): GhostResponse
    {
        return new IGhostResponse([
            'traceId' => $this->getTraceId(),
            'sessionId' => $this->getSessionId(),
            'errcode' => $errcode,
            'errmsg' => $errmsg,
            'outputs' => [],
        ]);
    }

    public function output(HostMsg $message, HostMsg ...$messages): GhostResponse
    {
        array_unshift($messages, $message);
        $input = $this->getInput();

        $outputs = array_map(function(HostMsg $message) use ($input) {
            return $input->output($message);
        }, $messages);

        return new IGhostResponse([
            'traceId' => $this->getTraceId(),
            'sessionId' => $this->getSessionId(),
            'outputs' => $outputs,
        ]);
    }

    public function getInput(): InputMsg
    {
        return $this->input;
    }


}