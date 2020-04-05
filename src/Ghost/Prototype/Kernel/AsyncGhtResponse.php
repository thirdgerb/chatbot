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
use Commune\Framework\Blueprint\Server\Response;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class AsyncGhtResponse implements Response
{

    /**
     * @var GhostInput
     */
    protected $ghostInput;


    /**
     * GhtAsyncResponse constructor.
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

    public function sendResponse(): void
    {
    }

    public function sendRejectResponse(): void
    {
    }

    public function sendFailureResponse(\Exception $e = null): void
    {
    }


}