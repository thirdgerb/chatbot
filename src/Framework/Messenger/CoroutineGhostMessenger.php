<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Messenger;

use Commune\Blueprint\Exceptions\CommuneLogicException;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Kernel\Protocals\IGhostRequest;
use Commune\Support\Utils\TypeUtils;
use Psr\Log\LoggerInterface;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;
use Commune\Contracts\Messenger\GhostMessenger;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CoroutineGhostMessenger implements GhostMessenger
{

    /**
     * @var Channel
     */
    protected $channel;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var int
     */
    protected $chanCapacity;

    /**
     * @var float
     */
    protected $timeout;

    /**
     * CoroutineGhostMessenger constructor.
     * @param LoggerInterface $logger
     * @param int $chanCapacity
     * @param float $timeout
     */
    public function __construct(LoggerInterface $logger, int $chanCapacity, float $timeout)
    {
        $this->logger = $logger;
        $this->chanCapacity = $chanCapacity;
        $this->timeout = $timeout;

        if (Coroutine::getCid() < 0) {
            throw new CommuneLogicException(
                Channel::class
                . ' should only instance in Swoole coroutine'
            );
        }

        $this->channel = new Channel($chanCapacity);
    }


    public function asyncSendRequest(GhostRequest $request, GhostRequest ...$requests): void
    {
        // 准备数据
        array_unshift($requests, $request);

        // 批量推送.
        foreach ($requests as $req) {

            // 推送.
            $success = $this->channel->push($req);

            // 记录推送失败日志, 很危险.
            if (! $success) {
                $this->logger->error(
                    __METHOD__
                    . ' send async request fail',
                    IGhostRequest::toLogContext($request)
                );
            }
        }
    }

    public function receiveAsyncRequest(): ? GhostRequest
    {
        $data = $this->channel->pop($this->timeout);

        // 必须传入的变量
        if (!$data instanceof GhostRequest) {
            $type = TypeUtils::getType($data);
            $this->logger->error(
                __METHOD__
                . "receive invalid channel package, $type given"
            );

            return null;
        }

        // 必须是异步请求
        if (!$data->isAsync()) {
            $this->logger->error(
                __METHOD__
                . ' only accept async request',
                IGhostRequest::toLogContext($data)
            );

            return null;
        }

        return $data;
    }


}