<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Prototype\Kernel;

use Commune\Framework\Blueprint\Intercom\ShellMsg;
use Commune\Shell\Contracts\ShlRequest;
use Commune\Shell\Contracts\ShlResponse;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class AsyncShlResponse implements ShlResponse
{
    protected $chatId;

    protected $traceId;

    protected $userId;

    public function __construct(ShlRequest $request)
    {
        $this->chatId = $request->getChatId();
        $this->traceId = $request->getTraceId();
        $this->userId = $request->getUserId();
    }

    public function getChatId(): string
    {
        return $this->chatId;
    }

    public function getTraceId(): string
    {
        return $this->traceId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function sendResponse(): void
    {
    }

    public function sendRejectResponse(): void
    {
    }

    public function sendFailureResponse(\Exception $e = null): void
    {
    }

    public function buffer(array $messages): void
    {
    }


}