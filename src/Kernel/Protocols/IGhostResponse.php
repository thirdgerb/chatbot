<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\Protocols;

use Commune\Blueprint\Kernel\Protocols\AppResponse;
use Commune\Protocols\Intercom\OutputMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Blueprint\Kernel\Protocols\GhostResponse;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $sessionId
 * @property-read string $traceId
 * @property-read string $batchId
 * @property-read int $errcode
 * @property-read string $errmsg
 * @property-read OutputMsg[] $outputs
 */
class IGhostResponse extends AbsMessage implements GhostResponse
{

    public static function instance(
        string $sessionId,
        string $traceId,
        string $batchId,
        array $outputs,
        int $errcode = 0,
        string $errmsg = ''
    ) : self
    {
        return new static([
            'sessionId' => $sessionId,
            'traceId' => $traceId,
            'batchId' => $batchId,
            'errcode' => $errcode,
            'errmsg' => $errmsg,
            'outputs' => $outputs
        ]);
    }

    public static function stub(): array
    {
        return [
            'sessionId' => '',
            'traceId' => '',
            'batchId' => '',
            'errcode' => 0,
            'errmsg' => '',
            'outputs' => [],
        ];
    }

    public static function relations(): array
    {
        return [
            'outputs[]' => OutputMsg::class,
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

    /*-------- message --------*/

    public function isEmpty(): bool
    {
        return false;
    }

    /*-------- request --------*/

    public function getTraceId(): string
    {
        return $this->traceId;
    }

    public function getBatchId(): string
    {
        return $this->batchId;
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

    public function getOutputs(): array
    {
        return $this->outputs;
    }

    public function __set_outputs(string $name, array $outputs) : void
    {
        $outputs = array_filter($outputs, function($value) {
            return $value instanceof OutputMsg;
        });

        $this->_data[$name] = $outputs;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /*-------- methods --------*/

    public function mergeOutputs(array $outputs): void
    {
        $buffer = $this->outputs;

        array_push($buffer, ...$outputs);
        $this->__set_outputs('outputs', $buffer);
    }

    public function hasOutputs(): bool
    {
        return count($this->outputs) > 0;
    }


    /*-------- Protocol --------*/

    public function getProtocolId(): string
    {
        return StringUtils::namespaceSlashToDot(static::class);
    }


}