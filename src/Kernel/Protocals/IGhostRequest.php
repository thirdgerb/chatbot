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
use Commune\Message\Abstracted\IComprehension;
use Commune\Message\Intercom\IInputMsg;
use Commune\Protocals\Comprehension;
use Commune\Protocals\HostMsg;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $traceId
 * @property-read bool $async
 *
 * @property-read string $fromApp
 * @property-read string $fromSession
 *
 * @property-read InputMsg $input
 *
 * @property-read array $env
 * @property-read string $entry
 * @property-read Comprehension $comprehension
 */
class IGhostRequest extends AbsMessage implements GhostRequest
{
    public static function instance(
        InputMsg $input,
        string $fromApp,
        string $fromSession,
        bool $async = false,
        string $entry = '',
        array $env = [],
        Comprehension $comprehension = null,
        string $traceId = ''
    ) : self
    {
        $data = [
            'input' => $input,
            'fromApp' => $fromApp,
            'fromSession' => $fromSession,
            'traceId' => $traceId,
            'async' => $async,
            'entry' => $entry,
            'env' => $env
        ];

        if (isset($comprehension)) $data['comprehension'] = $comprehension;

        return new static($data);
    }


    public static function stub(): array
    {
        return [
            'traceId' => '',
            'fromApp' => '',
            'fromSession' => '',
            'async' => false,
            'env' => [],
            'entry' => '',
            'input' => new IInputMsg(),
            'comprehension' => new IComprehension(),
        ];
    }

    public static function relations(): array
    {
        return [
            'input' => IInputMsg::class,
            'comprehension' => Comprehension::class,
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


    public function getTraceId(): string
    {
        $traceId = $this->traceId;
        return empty($traceId)
            ? $this->getInput()->getBatchId()
            : $traceId;
    }

    public function getBatchId(): string
    {
        return $this->getInput()->getBatchId();
    }


    /*-------- request --------*/

    public function isAsync(): bool
    {
        return $this->async;
    }

    public function isStateless(): bool
    {
        return $this->getInput()->getMessage() instanceof InputMsg;
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

    public function getFromApp(): string
    {
        return $this->fromApp;
    }

    public function getFromSession(): string
    {
        return $this->fromSession;
    }

    public function routeToSession(string $sessionId): void
    {
        $fromSession = $this->fromSession;
        $input = $this->getInput();

        $fromSession = empty($fromSession)
            ? $input->getSessionId()
            : $fromSession;

        $input->setSessionId($sessionId);
        $this->fromSession = $fromSession;
    }


    /*-------- methods --------*/

    public function response(int $errcode = AppResponse::SUCCESS, string $errmsg = ''): GhostResponse
    {
        return IGhostResponse::instance(
            $this->getTraceId(),
            $this->getBatchId(),
            [],
            $errcode,
            $errmsg
        );
    }

    public function output(
        string $appId,
        string $appName,
        HostMsg $message, HostMsg ...$messages
    ): GhostResponse
    {

        array_unshift($messages, $message);
        $input = $this->getInput();

        $outputs = array_map(function(HostMsg $message) use ($input, $appId, $appName) {
            return $input->output($message, $appId, $appName);
        }, $messages);

        return IGhostResponse::instance(
            $this->getTraceId(),
            $this->getBatchId(),
            $outputs
        );
    }

    public function getInput(): InputMsg
    {
        return $this->input;
    }


}