<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Ghost\TcpCo;

use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Contracts\Messenger\GhostMessenger;
use Psr\Log\LoggerInterface;
use Swoole\Coroutine\Channel;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TCGhostMessenger implements GhostMessenger
{


    /**
     * @var TCGServerOption
     */
    protected $option;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Channel
     */
    protected $chan;

    /**
     * TCGhostMessenger constructor.
     * @param TCGServerOption $option
     * @param LoggerInterface $logger
     * @param Channel $chan
     */
    public function __construct(TCGServerOption $option, LoggerInterface $logger, Channel $chan)
    {
        $this->option = $option;
        $this->logger = $logger;
        $this->chan = $chan;
    }


    public function asyncSendRequest(GhostRequest $request, GhostRequest ...$requests): void
    {
        array_unshift($requests, $request);
        foreach ($requests as $request) {
            $success = $this->chan->push($request, $this->option->chanTimeout);

            $this->logger->error(
                __METHOD__
                . ' push async request fail',
                [

                ]
            );
        }
    }


}