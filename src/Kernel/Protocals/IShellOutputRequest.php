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
 * @property-read string $sessionId
 * @property-read string $traceId
 * @property-read string $batchId
 * @property-read bool $async
 *
 * @property-read IntercomMsg[] $outputs
 */
class IShellOutputRequest extends AbsMessage implements ShellOutputRequest
{

    /**
     * 实例化一个异步请求.
     *
     * @param string $sessionId
     * @param string $traceId
     * @param string $batchId
     * @return IShellOutputRequest
     */
    public static function asyncInstance(
        string $sessionId,
        string $traceId,
        string $batchId
    ) : self
    {
        return static::instance(
            true,
            $sessionId,
            $traceId,
            $batchId,
            []
        );
    }

    /**
     * 实例化一个 Shell 输出请求.
     *
     * @param bool $async
     * @param string $sessionId
     * @param string $traceId
     * @param string $batchId
     * @param array $outputs
     * @return IShellOutputRequest
     */
    public static function instance(
        bool $async,
        string $sessionId,
        string $traceId,
        string $batchId,
        array $outputs = []
    ) : self
    {
        return new static([
            'sessionId' => $sessionId,
            'traceId' => $traceId,
            'batchId' => $batchId,
            'async' => $async,
            'outputs' => $outputs,
        ]);
    }

    public static function stub(): array
    {
        return [
            'sessionId' => '',
            'traceId' => '',
            'batchId' => '',
            'async' => false,
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

    public function getBatchId(): string
    {
        return $this->batchId;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function getTraceId(): string
    {
        return $this->traceId;
    }

    public function __set_outputs(string $name, array $outputs)  :void
    {
        $this->_data[$name] = array_filter($outputs, function($message) {
            return $message instanceof IntercomMsg;
        });
    }

    /*------ request ------*/

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
        return IShellOutputResponse::instance(
            $errcode,
            $errmsg,
            $this->outputs,
            $this->sessionId,
            $this->batchId,
            $this->traceId
        );
    }


    public function getProtocalId(): string
    {
        return StringUtils::namespaceSlashToDot(static::class);
    }

}