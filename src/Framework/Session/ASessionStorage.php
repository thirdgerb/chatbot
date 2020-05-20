<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Session;

use Commune\Blueprint\Exceptions\IO\SaveDataFailException;
use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Framework\Session\SessionStorage;
use Commune\Contracts\Cache;
use Commune\Support\RunningSpy\Spied;
use Commune\Support\RunningSpy\SpyTrait;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ASessionStorage implements SessionStorage, Spied
{
    use SpyTrait;

    const FIELD_LAST_ONCE_NAME = 'lastTimeOnceData';

    /**
     * @var string
     */
    protected $traceId;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Cache|null
     */
    protected $cache;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * AStorage constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->traceId = $session->getTraceId();

        if (!$session->isStateless()) {
            // 延迟加载, 免得不必要的创建了 Cache 的连接.
            $this->cache = $session->getContainer()->make(Cache::class);
            $this->initDataFromCache();
        }

        static::addRunningTrace($this->traceId, $this->traceId);

    }

    abstract public function getSessionKey(string $sessionName, string $sessionId) : string;

    protected function initDataFromCache() : void
    {
        if (!isset($this->cache)) {
            return;
        }

        $key = $this->getSessionKey(
            $this->session->getAppId(),
            $this->session->getId()
        );

        $cached = $this->cache->get($key);

        if (empty($cached)) {
            return;
        }

        $data = unserialize($cached);
        if (is_array($data)) {
            $data[self::FIELD_LAST_ONCE_NAME] = $data[self::FIELD_ONCE_NAME] ?? null;
            $this->data = $data;
        }
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function once(array $data): void
    {
        $this->data[self::FIELD_ONCE_NAME] = $data;
    }

    public function getOnce(): array
    {
        return $this->data[self::FIELD_ONCE_NAME]
            ?? $this->data[self::FIELD_LAST_ONCE_NAME]
            ?? [];
    }


    public function save(): void
    {
        if ($this->session->isStateless()) {
            return;
        }

        if (!isset($this->cache)) {
            return;
        }

        // 去掉上次请求的 once 数据.
        $data = $this->data;
        unset($data[self::FIELD_LAST_ONCE_NAME]);

        $str = serialize($data);
        $key = $this->getSessionKey(
            $this->session->getAppId(),
            $this->session->getId()
        );

        $ttl = $this->session->getSessionExpire();
        $ttl = $ttl > 0 ? $ttl : null;
        $success = $this->cache->set($key, $str, $ttl);

        // Storage 是 Session 的关键数据, 不能丢失.
        if (!$success) {
            throw new SaveDataFailException('storage data');
        }
    }

    public function __destruct()
    {
        $this->session = null;
        $this->cache = null;
        $this->data = [];

        static::removeRunningTrace($this->traceId);
    }


}