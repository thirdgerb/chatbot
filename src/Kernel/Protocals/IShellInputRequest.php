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
use Commune\Message\Abstracted\IComprehension;
use Commune\Message\Intercom\IInputMsg;
use Commune\Protocals\Comprehension;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Blueprint\Kernel\Protocals\ShellInputRequest;
use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $traceId
 * @property-read bool $async
 * @property-read InputMsg $input
 *
 * @property-read array $env
 * @property-read string $entry
 * @property-read Comprehension $comprehension
 */
class IShellInputRequest extends AbsMessage implements ShellInputRequest
{
    public static function instance(
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
            'async' => $async,
            'input' => $input,
            'entry' => $entry,
            'env' => $env,
        ];

        if (isset($comprehension)) $data['comprehension'] = $comprehension;

        return new static($data);
    }

    public static function stub(): array
    {
        return [
            'traceId' => '',
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

    /*------- request -------*/

    public function getTraceId(): string
    {
        $traceId = $this->traceId;
        return empty($traceId)
            ? $this->getInput()->getMessageId()
            : $traceId;
    }

    public function getSessionId(): string
    {
        return $this->getInput()->getSessionId();
    }


    public function isAsync(): bool
    {
        return $this->async;
    }

    public function getInput(): InputMsg
    {
        return $this->input;
    }

    public function isInvalid(): ? string
    {
        return $this->getInput()->isInvalid();
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


    /*------- methods -------*/

    public function response(
        int $errcode = AppResponse::SUCCESS,
        string $errmsg = ''
    ): ShellInputResponse
    {
        return IShellInputResponse::instance(
            $errcode,
            $errmsg,
            $this->input,
            $this->entry,
            $this->env,
            $this->comprehension,
            $this->async,
            $this->traceId
        );

    }


    /*------- protocal -------*/

    public function getProtocalId(): string
    {
        return StringUtils::namespaceSlashToDot(static::class);
    }

}