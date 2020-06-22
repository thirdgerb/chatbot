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
use Commune\Message\Abstracted\IComprehension;
use Commune\Message\Intercom\IInputMsg;
use Commune\Protocals\Comprehension;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Protocals\IntercomMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Blueprint\Kernel\Protocals\ShellInputResponse;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $traceId
 * @property-read int $errcode
 * @property-read string $errmsg
 * @property-read bool $async
 *
 * @property-read InputMsg $input
 * @property-read array $env
 * @property-read string $entry
 * @property-read Comprehension $comprehension
 */
class IShellInputResponse extends AbsMessage implements ShellInputResponse
{

    public static function instance(
        int $errcode,
        string $errmsg,
        bool $async,
        InputMsg $input,
        string $entry = '',
        array $env = [],
        Comprehension $comprehension = null,
        string $traceId = ''
    ) : self
    {
        $data = [
            'traceId' => $traceId,
            'errcode' => $errcode,
            'errmsg' => $errmsg,
            'async' => $async,
            'input' => $input,
            'env' => $env,
            'entry' => $entry,
            'comprehension' => $comprehension ?? new IComprehension(),
        ];
        if (isset($comprehension)) $data['comprehension'] = $comprehension;
        return new static($data);
    }

    public static function outputResponse(
        bool $async,
        InputMsg $input,
        array $outputs,
        string $traceId = ''
    ) : self
    {
        $data = [
            'traceId' => $traceId,
            'errcode' => AppResponse::SUCCESS,
            'errmsg' => '',
            'async' => $async,
            'input' => $input,
            'outputs' => $outputs,
        ];
        return new static($data);
    }

    public static function stub(): array
    {
        return [
            'traceId' => '',
            'errcode' => 0,
            'errmsg' => '',
            'async' => false,
            'input' => new IInputMsg(),
            'env' => [],
            'entry' => '',
            'comprehension' => new IComprehension(),
            'outputs' => [],
        ];
    }

    public static function relations(): array
    {
        return [
            'input' => InputMsg::class,
            'comprehension' => Comprehension::class,
            'outputs[]' => IntercomMsg::class,
        ];
    }

    public function isEmpty(): bool
    {
        return false;
    }


    public function fill(array $data): void
    {
        if (empty($data['errmsg'])) {
            $errcode = $data['errcode'] ?? 0;
            $data['errmsg'] = AppResponse::DEFAULT_ERROR_MESSAGES[$errcode];
        }

        parent::fill($data);
    }

    /*-------- request --------*/

    public function getTraceId(): string
    {
        $traceId = $this->traceId;
        return empty($traceId)
            ? $this->getBatchId()
            : $traceId;
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
        return !$this->hasOutputs() && $this->errcode < AppResponse::FAILURE_CODE_START;
    }

    public function getInput(): InputMsg
    {
        return $this->input;
    }

    public function isAsync(): bool
    {
        return $this->async;
    }

    public function getBatchId(): string
    {
        return $this->getInput()->getBatchId();
    }

    public function getEnv(): array
    {
        return $this->env;
    }

    public function getEntry(): string
    {
        return $this->entry;
    }

    public function hasOutputs(): bool
    {
        $outputs = $this->getOutputs();
        return count($outputs) > 0;
    }

    public function getOutputs(): array
    {
        return $this->outputs;
    }


    public function getComprehension(): Comprehension
    {
        return $this->comprehension;
    }

    public function getSessionId(): string
    {
        return $this->getInput()->getSessionId();
    }


    /*-------- protocal --------*/

    public function getProtocalId(): string
    {
        return StringUtils::namespaceSlashToDot(static::class);
    }


}