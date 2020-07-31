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

use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Blueprint\Shell;
use Commune\Contracts\Messenger\Broadcaster;
use Commune\Kernel\Protocals\IShellOutputRequest;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class LocalBroadcaster implements Broadcaster
{
    /**
     * @var Shell
     */
    protected $shell;

    protected $chan = [];

    /**
     * LocalBroadcaster constructor.
     * @param Shell $shell
     */
    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }


    public function publish(
        GhostRequest $request,
        GhostResponse $response,
        array $routes
    ): void
    {
        $shellRequest = IShellOutputRequest::asyncInstance(
            $request->getSessionId(),
            $request->getTraceId(),
            $request->getBatchId(),
            $request->getInput()->getCreatorId(),
            $request->getInput()->getCreatorName()
        );

        array_push($this->chan, $shellRequest);
    }

    public function subscribe(
        callable $callback,
        string $shellId,
        string $shellSessionId = null
    ): void
    {
        $chan = "$shellId/$shellSessionId";
        while($request = array_shift($this->chan)) {
            $callback($chan, $request);
        }
    }


}