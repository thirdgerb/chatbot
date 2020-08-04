<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Messenger\Broadcaster;

use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Contracts\Messenger\Broadcaster;
use Psr\Log\LoggerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsBroadcaster implements Broadcaster
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var string[]
     */
    protected $listeningShells;

    /**
     * AbsBroadcaster constructor.
     * @param LoggerInterface $logger
     * @param array $listeningShells
     */
    public function __construct(LoggerInterface $logger, array $listeningShells)
    {
        $this->listeningShells = $listeningShells;
        $this->logger = $logger;
    }

    abstract public function doPublish(
        string $shellId,
        string $shellSessionId,
        string $publish
    ) : void;

    abstract public function doSubscribe(
        callable $callback,
        string $shellId,
        string $shellSessionId = null
    ) : void;

    public function publish(
        GhostRequest $request,
        GhostResponse $response,
        array $routes
    ): void
    {
        $selfShellId = $request->getFromApp();
        $selfSessionId = $request->getSessionId();

        // 完善路由配置.
        $routes = $this->prepareRoutes($routes, $selfSessionId);

        // 如果是同步消息, 则不广播.
        unset($routes[$selfShellId]);

        // 异步消息的话, 自己也要广播.
        if ($request->isAsync()) {
            $routes[$selfShellId] = $request->getFromSession();
        }

        $batchId = $request->getBatchId();
        $traceId = $request->getTraceId();
        $input = $request->getInput();
        $creatorId = $input->getCreatorId();
        $creatorName = $input->getCreatorName();

        foreach ($routes as $shellId => $shellSessionId) {

            $publish = BroadcastProtocal::serializePublish(
                $shellId,
                $shellSessionId,
                $batchId,
                $traceId,
                $creatorId,
                $creatorName
            );

            if (CommuneEnv::isDebug()) {
                $this->logger->debug(
                    __METHOD__
                    . " publish data $publish",
                    [
                        'shell' => $shellId,
                        'session' => $shellSessionId,
                    ]
                );
            }

            $this->doPublish(
                $shellId,
                $shellSessionId,
                $publish
            );
        }
    }

    protected function prepareRoutes(array $routes, string $selfSessionId) : array
    {
        if (empty($this->listeningShells)) {
            return $routes;
        }

        // 如果存在必须监听的 Shell, 则主动广播.
        foreach ($this->listeningShells as $shellId) {
            if (!isset($routes[$shellId])) {
                $routes[$shellId] = $selfSessionId;
            }
        }

        return $routes;
    }

    public function subscribe(
        callable $callback,
        string $shellId,
        string $shellSessionId = null
    ): void
    {
        $this->doSubscribe(
            function($chan, string $send) use ($callback){

                $request = BroadcastProtocal::unserializePublish($send);
                if (empty($request)) {
                    return;
                }

                $callback($chan, $request);

            },
            $shellId,
            $shellSessionId
        );
    }


}