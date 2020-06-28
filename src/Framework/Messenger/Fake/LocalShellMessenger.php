<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Messenger\Fake;

use Commune\Blueprint\Ghost;
use Commune\Blueprint\Kernel\Handlers\GhostRequestHandler;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Contracts\Messenger\ShellMessenger;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class LocalShellMessenger implements ShellMessenger
{

    /**
     * @var Ghost
     */
    protected $ghost;

    /**
     * ArrMessenger constructor.
     * @param Ghost $ghost
     */
    public function __construct(Ghost $ghost)
    {
        $this->ghost = $ghost;
    }


    public function sendGhostRequest(GhostRequest $request): GhostResponse
    {
        /**
         * @var GhostResponse $response
         */
        $response = $this->ghost->handleRequest($request, GhostRequestHandler::class);
        return $response;
    }
}