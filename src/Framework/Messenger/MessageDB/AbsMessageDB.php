<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Messenger\MessageDB;

use Commune\Contracts\Cache;
use Commune\Contracts\Messenger\MessageDB;
use Commune\Protocals\IntercomMsg;
use Commune\Support\Babel\Babel;
use Commune\Support\Utils\TypeUtils;
use Psr\Log\LoggerInterface;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsMessageDB implements MessageDB
{
    /**
     * @var Cache
     */
    protected $cache;


    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var int
     */
    protected $cacheTtl = 10;

    /**
     * AbsMessageDB constructor.
     * @param Cache $cache
     * @param LoggerInterface $logger
     * @param int $cacheTtl
     */
    public function __construct(Cache $cache, LoggerInterface $logger, int $cacheTtl = 10)
    {
        $this->cache = $cache;
        $this->logger = $logger;
        $this->cacheTtl = $cacheTtl;
    }


    /**
     * @param string $traceId
     * @param string $fromApp
     * @param string $fromSession
     * @param string $batchId
     * @param IntercomMsg ...$outputs
     */
    abstract public function saveBatchMessages(
        string $traceId,
        string $fromApp,
        string $fromSession,
        string $batchId,
        IntercomMsg ...$outputs
    ) : void;

    /**
     * @param string $batchId
     * @return IntercomMsg[]
     */
    abstract public function loadBatchMessages(string $batchId) : array;

    /*-------- 保存消息 --------*/

    public function recordMessages(
        string $traceId,
        string $fromApp,
        string $fromSession,
        IntercomMsg $input,
        IntercomMsg ...$outputs
    ): void
    {
        $batchId = $input->getBatchId();
        array_unshift($outputs, $input);

        // 消息缓存是一个同步行为. 因为影响到下游的逻辑.
        $this->cacheMessages($batchId, $outputs);

        // 消息保存建议使用 异步任务/协程/管道 等方式提交 .
        // 没有必要阻塞. 推荐使用 Channel
        $this->saveBatchMessages($traceId, $fromApp, $fromSession, $batchId, ...$outputs);
    }

    public function cacheMessages(string $batchId, array $outputs) : void
    {
        $serialized = array_map([Babel::class, Babel::FUNC_SERIALIZE], $outputs);
        $serialized = json_encode($serialized);
        $key = $this->makeBatchCacheKey($batchId);

        try {
            $this->cache->set($key, $serialized, $this->cacheTtl);

        } catch (\Throwable $e) {
            $this->logger->error($e);
        }
    }

    protected function makeBatchCacheKey(string $batchId) : string
    {
        return "messages:batch:$batchId";
    }


    public function fetchBatch(string $batchId): array
    {
        $key = $this->makeBatchCacheKey($batchId);

        $serialized = null;
        try {

            $serialized = $this->cache->get($key);

        } catch (\Throwable $e) {
            $this->logger->error($e);
        }

        return $this->unserializeCached($batchId, $serialized)
            ?? $this->loadBatchMessages($batchId);
    }


    /**
     * @param string $batchId
     * @param null|string $serialized
     * @return IntercomMsg[]|null
     */
    protected function unserializeCached(string $batchId, ? string $serialized) : ? array
    {
        if (empty($serialized)) {
            return null;
        }


        $decode = json_decode($serialized, true);
        if (!is_array($decode)) {
            return null;
        }

        // 不符合逻辑.
        if (empty($decode)) {
            $this->logger->warning(
                static::class . '::'. __FUNCTION__
                . " get empty cache messages that batchId is $batchId"
            );
            return [];
        }

        $outputs = [];

        foreach ($decode as $babelSerialized) {

            $message = Babel::unserialize($babelSerialized);
            if (!$message instanceof IntercomMsg) {
                $type = TypeUtils::getType($message);
                $this->logger->warning(
                    static::class . '::'. __FUNCTION__
                    . " get decoded message serialized string $babelSerialized that can not unserialize to intercom message, $type given"
                );
                continue;
            }

            $outputs[] = $message;
        }

        return $outputs;
    }

    public function fetch(callable $fetcher): array
    {
        return $fetcher($this);
    }


}