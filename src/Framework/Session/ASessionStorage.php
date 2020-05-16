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
            $this->session->getName(),
            $this->session->getSessionId()
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

        $str = serialize($this->data);
        $key = $this->getSessionKey(
            $this->session->getName(),
            $this->session->getSessionId()
        );

        $ttl = $this->session->getSessionExpire();
        $ttl = $ttl > 0 ? $ttl : null;
        $success = $this->cache->set($key, $str, $ttl);

        // Storage 是 Session 的关键数据, 不能丢失.
        if (!$success) {
            throw new SaveDataFailException(
                __METHOD__,
                $this->traceId
            );
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