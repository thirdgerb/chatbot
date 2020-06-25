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

use Commune\Kernel\Protocals\IGhostRequest;
use Commune\Support\Babel\Babel;
use Commune\Support\Swoole\ClientOption;
use Commune\Support\Utils\TypeUtils;
use Psr\Log\LoggerInterface;
use Swoole\ConnectionPool;
use Swoole\Coroutine\Client;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Kernel\Protocals\IGhostResponse;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Contracts\Messenger\ShellMessenger;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ShlMessengerBySwlCoTcp implements ShellMessenger
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ClientOption
     */
    protected $option;

    /*---- cached ----*/

    /**
     * @var ConnectionPool
     */
    protected $pool;

    /**
     * @var string
     */
    protected $error;

    /**
     * ShlMessengerBySwlCoTcp constructor.
     * @param LoggerInterface $logger
     * @param ClientOption $option
     */
    public function __construct(LoggerInterface $logger, ClientOption $option)
    {
        $this->logger = $logger;
        $this->option = $option;
        $this->initPool();
    }

    protected function initPool() : void
    {
        $pool = new ConnectionPool(
            [$this, 'createClient'],
            $this->option->poolSize
        );

        $this->pool = $pool;
    }

    public function createClient() : Client
    {
        $client = new Client(SWOOLE_SOCK_TCP);

        $client->connect(
            $this->option->host,
            $this->option->port,
            $this->option->connectTimeout
        );

        return $client;
    }


    public function sendGhostRequest(GhostRequest $request): GhostResponse
    {
        try {

            /**
             * @var Client $client
             */
            $client = $this->pool->get();

        } catch (\Exception $e) {
            $this->logger->error(
                $e,
                IGhostRequest::toLogContext($request)
            );

            return $this->fail($request, $e->getMessage());
        }

        $se = Babel::serialize($request);
        $success = $client->send($se);
        if (!$success) {
            return $this->fail($request, 'send request to ghost fail');
        }

        $timeout = $this->option->receiveTimeout;
        $response = $client->recv($timeout);
        if (empty($response)) {
            return $this->fail($request, "send request to ghost timeout after $timeout second");
        }

        $this->pool->put($client);
        $un = Babel::unserialize($response);
        if (!TypeUtils::isA($un, GhostResponse::class)) {
            $type = TypeUtils::getType($un);
            return $this->fail($request, "receive output can not Babel unserialize to GhostResponse, $type given");
        }

        return $un;
    }

    protected function fail(GhostRequest $request, string $error = '') : GhostResponse
    {
        $this->logger->error(
            __METHOD__
            . ' send request fail: '
            . $error,
            IGhostRequest::toLogContext($request)
        );

        return IGhostResponse::instance(
            $request->getFromSession(),
            $request->getTraceId(),
            $request->getBatchId(),
            [],
            AppResponse::HOST_REQUEST_FAIL,
            $error
        );
    }


}