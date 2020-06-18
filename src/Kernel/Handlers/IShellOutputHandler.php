<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\Handlers;

use Commune\Blueprint\Kernel\Handlers\ShellOutputHandler;
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;
use Commune\Blueprint\Shell\ShellSession;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShellOutputHandler implements ShellOutputHandler
{
    /**
     * @var ShellSession
     */
    protected $session;


    public function __invoke(ShellOutputRequest $request): ShellOutputResponse
    {
        $response = $request->validate();

        if (isset($response)) {
            return $response;
        }

        if ($request->isAsync()) {
            $this->fillOutputsInRequest($request);
        }


    }


    protected function fillOutputInRequest(ShellOutputRequest $request) : ShellOutputRequest
    {

    }
}