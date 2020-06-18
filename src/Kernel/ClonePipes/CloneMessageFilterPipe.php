<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\ClonePipes;

use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Message\Host\Convo\IUnsupportedMsg;
use Commune\Protocals\HostMsg;
use Commune\Protocals\HostMsg\Convo\UnsupportedMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CloneMessageFilterPipe extends AClonePipe
{

    /**
     * @var  string[]
     */
    protected $unsupportedMessages = [];

    protected function doHandle(GhostRequest $request, \Closure $next): GhostResponse
    {
        $message = $request->getInput()->getMessage();
        // 消息过滤.
        $response = $this->filterMessage($request, $message);
        if (isset($response)) {
            $this->cloner->noState();
            return $response;
        }

        return $next($request);
    }


    /**
     * 过滤消息类型. 不支持的消息就算了.
     *
     * @param GhostRequest $request
     * @param HostMsg $message
     * @return GhostResponse|null
     */
    protected function filterMessage(GhostRequest $request, HostMsg $message) : ? GhostResponse
    {
        // 进行消息过滤.
        if ($message instanceof UnsupportedMsg) {
            return $this->unsupported($request);
        }

        if (empty($this->unsupportedMessages)) {
            return null;
        }

        foreach ($this->unsupportedMessages as $messageType) {
            if (is_a($messageType, $messageType, TRUE)) {
                return $this->unsupported($request);
            }
        }

        return null;
    }

    protected function unsupported(GhostRequest $request) : GhostResponse
    {
        return $request->output(new IUnsupportedMsg());
    }


}