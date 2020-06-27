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

use Commune\Contracts\Redis\RedisPool;
use Commune\Framework\Redis\SwlRedisPool;
use Commune\Support\Swoole\RedisOption;
use Psr\Log\LoggerInterface;
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
    protected $writePool;

    /**
     * @var RedisPool
     */
    protected $readPool;

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

    protected function getPublishPool() : RedisPool
    {
        if (isset($this->writePool)) {
            return $this->writePool;
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

        return $this->writePool = new SwlRedisPool($config, $this->pubSize);
    }

    protected function getSubscribePool() : RedisPool
    {
        if (isset($this->readPool)) {
            return $this->readPool;
        }

        $option = $this->option;
        $config = new RedisConfig();
        $config = $config
            ->withHost($option->host)
            ->withPort($option->port)
            ->withAuth($option->auth)
            ->withDbIndex($option->dbIndex)
            ->withTimeout($option->timeout)
            ->withReadTimeout(-1)
            ->withRetryInterval($option->retryInterval)
            ->withReserved($option->reserved);;

        return $this->readPool = new SwlRedisPool($config, $this->subSize);
    }

    public function doPublish(
        string $shellId,
        string $shellSessionId,
        string $publish
    ): void
    {
        try {

            $connection = $this->getPublishPool()->get();
            $client = $connection->get();

            $chan = static::makeChannel($shellId, $shellSessionId);
            $client->publish($chan, $publish);
            $connection->release();

        } catch (\Throwable $e) {
            isset($client) and $client->close();
            $this->logger->error($e);
        }
    }

    public static function makeChannel(string $shellId, string $sessionId) : string
    {
        return "commune/$shellId/$sessionId";
    }

    public function doSubscribe(
        callable $callback,
        string $shellId,
        string $shellSessionId = null
    ): void
    {
        $chan = static::makeChannel($shellId, $shellSessionId ?? '*');

        while (true) {
            try {

                $connection = $this->getSubscribePool()->get();
                $client  = $connection->get();

                $client->subscribe([$chan], function ($redis, $chan, $message) use ($callback){
                    $callback($chan, $message);
                });

            } catch (\Throwable $e) {

                isset($client) and $client->close();

            }
        }
    }

}