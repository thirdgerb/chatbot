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
        ];
    }

    public static function relations(): array
    {
        return [
            'input' => InputMsg::class,
            'comprehension' => Comprehension::class
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

    public function isSuccess(): bool
    {
        return $this->errcode < AppResponse::FAILURE_CODE_START;
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

    public function getComprehension(): Comprehension
    {
        return $this->comprehension;
    }


    /*-------- protocal --------*/

    public function getProtocalId(): string
    {
        return StringUtils::namespaceSlashToDot(static::class);
    }


}