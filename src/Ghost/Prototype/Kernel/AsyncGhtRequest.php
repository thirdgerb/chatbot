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
     * AsyncGhostInputReq constructor.
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
        return false;
    }

    public function validate(): bool
    {
        return true;
    }

    public function getBrief(): string
    {
        return $this->ghostInput->toJson();
    }

    public function getLogContext(): array
    {
        return [
            'shn' => $this->ghostInput->shellName,
            'cid' => $this->ghostInput->chatId,
            'tid' => $this->ghostInput->traceId,
            'sid' => $this->ghostInput->sceneId,
            'mid' => $this->ghostInput->messageId
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


}