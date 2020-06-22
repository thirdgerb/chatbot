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

use Commune\Protocals\Intercom\OutputMsg;
use Commune\Protocals\IntercomMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Support\Utils\StringUtils;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $sessionId
 * @property-read string $batchId
 * @property-read string $traceId
 * @property-read int $errcode
 * @property-read string $errmsg
 * @property-read OutputMsg[] $outputs
 */
class IShellOutputResponse extends AbsMessage implements ShellOutputResponse
{

    public static function instance(
        int $errcode,
        string $errmsg,
        array $outputs,
        string $sessionId,
        string $batchId,
        string $traceId
    ) : self
    {
        return new static([
            'errcode' => $errcode,
            'errmsg' => $errmsg,
            'sessionId' => $sessionId,
            'batchId' => $batchId,
            'outputs' => $outputs,
            'traceId' => $traceId,
        ]);

    }

    public static function stub(): array
    {
        return [
            'errcode' => 0,
            'errmsg' => '',
            'sessionId' => '',
            'batchId' => '',
            'traceId' => '',
            'outputs' => []
        ];
    }

    public static function relations(): array
    {
        return [
            'outputs[]' => IntercomMsg::class,
        ];
    }


    public function fill(array $data): void
    {
        if (empty($data['errmsg'])) {
            $errcode = $data['errcode'] ?? 0;
            $data['errmsg'] = AppResponse::DEFAULT_ERROR_MESSAGES[$errcode];
        }

        parent::fill($data);
    }

    /*------ message ------*/

    public function isEmpty(): bool
    {
        return false;
    }

    /*------ protocal ------*/

    public function getProtocalId(): string
    {
        return StringUtils::namespaceSlashToDot(static::class);
    }

    /*------ request ------*/

    public function getOutputs(): array
    {
        return $this->outputs;
    }

    public function __set_outputs(string $name, array $outputs)  :void
    {
        $this->_data[$name] = array_filter($outputs, function($message) {
            return $message instanceof IntercomMsg;
        });
    }

    public function getBatchId(): string
    {
        return $this->batchId;
    }


    public function getTraceId(): string
    {
        return $this->traceId;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function getErrcode(): int
    {
        return $this->errcode;
    }

    public function getErrmsg(): string
    {
        return $this->errmsg;
    }

    public function isForward(): bool
    {
        return $this->errcode < AppResponse::FAILURE_CODE_START;
    }


}