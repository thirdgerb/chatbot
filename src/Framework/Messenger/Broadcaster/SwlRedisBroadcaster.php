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
use Commune\Contracts\Redis\RedisPool;
use Commune\Framework\Redis\SwlRedisPool;
use Commune\Support\Swoole\RedisOption;
use Psr\Log\LoggerInterface;
use Swoole\Coroutine;
use Swoole\Database\RedisConfig;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SwlRedisBroadcaster extends AbsBroadcaster
{

    /**
     * @var RedisOption
     */
    protected $option;

    /**
     * @var RedisPool
     */
    protected $pool;

    /**
     * @var int
     */
    protected $pubSize;

    /**
     * @var int
     */
    protected $subSize;

    /**
     * SwlRedisBroadcaster constructor.
     * @param RedisOption $option
     * @param LoggerInterface $logger
     * @param array $listeningShells
     * @param int $pubSize
     * @param int $subSize
     */
    public function __construct(
        RedisOption $option,
        LoggerInterface $logger,
        array $listeningShells,
        int $pubSize,
        int $subSize
    )
    {
        $this->option = $option;
        $this->pubSize = $pubSize;
        $this->subSize = $subSize;
        parent::__construct($logger, $listeningShells);
    }

    protected function getRedisPool() : RedisPool
    {
        if (isset($this->pool)) {
            return $this->pool;
        }
        $option = $this->option;
        $config = new RedisConfig();
        $config = $config
            ->withHost($option->host)
            ->withPort($option->port)
            ->withAuth($option->auth)
            ->withDbIndex($option->dbIndex)
            ->withTimeout($option->timeout)
            ->withReadTimeout($option->readTimeout)
            ->withRetryInterval($option->retryInterval)
            ->withReserved($option->reserved);;

        return $this->pool = new SwlRedisPool($config, $this->pubSize);
    }


    public function doPublish(
        string $shellId,
        string $shellSessionId,
        string $publish
    ): void
    {
        try {

            $connection = $this->getRedisPool()->get();
            $client = $connection->get();

            $shellChan = static::makeChannel($shellId, '');
            // session chan
            $sessionChan = static::makeChannel($shellId, $shellSessionId);

            $client->publish($shellChan, $publish);
            $client->publish($sessionChan, $publish);

            $connection->release();

            if (CommuneEnv::isDebug()) {
                $this->logger->debug(__METHOD__ . " publish $shellChan/$sessionChan: $publish");
            }

        } catch (\Throwable $e) {
            isset($client) and $client->close();
            $this->logger->error($e);
        }
    }

    public static function makeChannel(string $shellId, string $sessionId) : string
    {
        $sessionId = empty($sessionId) ? '' : "/$sessionId";
        return "commune/$shellId$sessionId";
    }

    public function doSubscribe(
        callable $callback,
        string $shellId,
        string $shellSessionId = null
    ): void
    {
        $chan = static::makeChannel($shellId, $shellSessionId ?? '');

        $expErr = 0;
        while (true) {
            try {

                $connection = $this->getRedisPool()->get();

                $client  = $connection->get();

                $client->setOption(\Redis::OPT_READ_TIMEOUT, -1);

                $client->subscribe([$chan], function ($redis, $chan, $message) use ($callback){
                    $callback($chan, $message);
                });

                // 成功监听, 计数器清零.
                $expErr = 0;

            } catch (\Throwable $e) {
                // 连接错误的话计数器增加.
                $expErr ++;

                if ($expErr > 3) {
                    throw $e;
                }

                $this->logger->error($e);
                Coroutine::sleep(1);

            } finally {

                // 不用把连接返回连接池了.
                isset($client) and $client->close();
            }
        }
    }

}