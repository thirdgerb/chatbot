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
use Commune\Ghost\Contracts\GhostResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class AsyncGhostResponse implements GhostResponse
{
    /**
     * @var GhostInput
     */
    protected $ghostInput;

    /**
     * AsyncGhostRequest constructor.
     * @param GhostInput $ghostInput
     */
    public function __construct(GhostInput $ghostInput)
    {
        $this->ghostInput = $ghostInput;
    }

    public function getChatId(): string
    {
        return $this->ghostInput->chatId;
    }

    public function getTraceId(): string
    {
        return $this->ghostInput->traceId;
    }

    public function getUserId(): string
    {
        return $this->ghostInput->shellMessage->scope->userId;
    }

    public function sendResponse(): void
    {
        // TODO: Implement sendResponse() method.
    }

    public function sendRejectResponse(): void
    {
        // TODO: Implement sendRejectResponse() method.
    }

    public function sendFailureResponse(\Exception $e = null): void
    {
        // TODO: Implement sendFailureResponse() method.
    }


}