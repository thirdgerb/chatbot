<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\MessengerFaker;

use Commune\Blueprint\Ghost;
use Commune\Blueprint\Kernel\Handlers\GhostRequestHandler;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Contracts\Messenger\Messenger;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ArrMessenger implements Messenger
{

    /**
     * @var Ghost
     */
    protected $ghost;

    /**
     * @var GhostRequest[]
     */
    protected $requests = [];

    /**
     * ArrMessenger constructor.
     * @param Ghost $ghost
     */
    public function __construct(Ghost $ghost)
    {
        $this->ghost = $ghost;
    }


    public function sendInput2Ghost(GhostRequest $request): GhostResponse
    {
        /**
         * @var GhostResponse $response
         */
        $response = $this->ghost->handleRequest($request, GhostRequestHandler::class);
        return $response;
    }

    public function asyncSend2Ghost(GhostRequest $request, GhostRequest ...$requests): void
    {
        array_unshift($requests, $request);
        array_push($this->requests, ...$requests);
    }

    public function popAsyncGhostRequest(): ? GhostRequest
    {
        return array_pop($this->requests);
    }


}