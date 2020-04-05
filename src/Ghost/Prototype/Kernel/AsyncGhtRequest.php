<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Kernel;

use Commune\Framework\Blueprint\Intercom\GhostInput;
use Commune\Ghost\Contracts\GhtRequest;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class AsyncGhtRequest implements GhtRequest
{
    /**
     * @var GhostInput
     */
    protected $ghostInput;

    /**
     * AsyncGhtRequest constructor.
     * @param GhostInput $ghostInput
     */
    public function __construct(GhostInput $ghostInput)
    {
        $this->ghostInput = $ghostInput;
    }


    public function getGhostInput(): GhostInput
    {
        return $this->ghostInput;
    }

    public function isStateless(): bool
    {
        return $this->ghostInput->stateless;
    }

    public function validate(): bool
    {
        return true;
    }

    public function getBrief(): string
    {
        return $this->ghostInput->toJson();
    }

    public function getSessionId(): string
    {
        return $this->ghostInput->shellMessage->scope->sessionId;
    }


    public function getLogContext(): array
    {
        return [
            'chatId' => $this->getChatId(),
            'shellName' => $this->ghostInput->shellName,
            'sessionId' => $this->getSessionId(),
            'sceneId' => $this->getSceneId(),
            'messageId' => $this->getMessageId()
        ];
    }

    public function getInput()
    {
        return $this->ghostInput;
    }

    public function getTraceId(): string
    {
        return $this->ghostInput->traceId;
    }

    public function getUuid(): string
    {
        return $this->ghostInput->messageId;
    }

    public function getChatId(): string
    {
        return $this->ghostInput->chatId;
    }

    public function getUserId(): string
    {
        return $this->ghostInput->shellMessage->scope->userId;
    }

    public function getMessageId(): string
    {
        return $this->ghostInput->messageId;
    }

    public function getSceneId(): ? string
    {
        return $this->ghostInput->sceneId;
    }


}