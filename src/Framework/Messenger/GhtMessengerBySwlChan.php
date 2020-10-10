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

use Swoole\Coroutine;
use Swoole\Coroutine\Channel;
use Commune\Contracts\Messenger\GhostMessenger;
use Commune\Blueprint\Exceptions\CommuneLogicException;
use Commune\Blueprint\Kernel\Protocols\GhostRequest;
use Commune\Kernel\Protocols\LogContext;
use Commune\Support\Utils\TypeUtils;
use Psr\Log\LoggerInterface;


/**
 * Ghost 发送异步消息的 Swoole Channel 实现.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class GhtMessengerBySwlChan implements GhostMessenger
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
    protected $popTimeout;

    /**
     * @var float
     */
    protected $pushTimeout;

    /**
     * GhtMessengerBySwlChan constructor.
     * @param LoggerInterface $logger
     * @param int $chanCapacity
     * @param float $pushTimeout
     * @param float $popTimeout
     */
    public function __construct(
        LoggerInterface $logger,
        int $chanCapacity,
        float $pushTimeout = -1,
        float $popTimeout = -1
    )
    {
        $this->logger = $logger;
        $this->chanCapacity = $chanCapacity;
        $this->pushTimeout = $pushTimeout;
        $this->popTimeout = $popTimeout;

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
            $success = $this->channel->push($req, $this->pushTimeout);

            // 记录推送失败日志, 很危险.
            if (! $success) {
                $this->logger->error(
                    static::class . '::'. __FUNCTION__
                    . ' send async request fail',
                    LogContext::requestToContext($request)
                );
            }
        }
    }

    public function receiveAsyncRequest(): ? GhostRequest
    {
        $data = $this->channel->pop($this->popTimeout);

        // 必须传入的变量
        if (!$data instanceof GhostRequest) {
            $type = TypeUtils::getType($data);
            $this->logger->error(
                static::class . '::'. __FUNCTION__
                . " receive invalid channel package, $type given"
            );

            return null;
        }

        // 必须是异步请求
        if (!$data->isAsync()) {
            $this->logger->error(
                static::class . '::'. __FUNCTION__
                . ' only accept async request',
                LogContext::requestToContext($data)
            );

            return null;
        }

        return $data;
    }


}