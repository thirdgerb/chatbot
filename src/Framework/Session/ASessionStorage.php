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

use Commune\Contracts\Cache;
use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Framework\Session\SessionStorage;
use Commune\Blueprint\Exceptions\IO\SaveDataFailException;
use Commune\Framework\Spy\SpyAgency;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ASessionStorage implements SessionStorage
{

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

        SpyAgency::incr(static::class);
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

        SpyAgency::decr(static::class);
    }


}