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
use Commune\Support\Arr\TArrayAccessToMutator;
use Commune\Support\Arr\TArrayData;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ASessionStorage implements SessionStorage
{
    use TArrayData, TArrayAccessToMutator;

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
     * AStorage constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->traceId = $session->getTraceId();

        if (! $this->isStateless()) {
            $this->cache = $session->getContainer()->make(Cache::class);
            $this->initDataFromCache();
        }

        SpyAgency::incr(static::class);
    }



    abstract public function isStateless() : bool;

    abstract public function getSessionKey(string $appId, string $sessionId) : string;

    protected function initDataFromCache() : void
    {
        if (!isset($this->cache)) {
            return;
        }

        $key = $this->getSessionKey(
            $this->session->getAppId(),
            $this->session->getSessionId()
        );

        $cached = $this->cache->get($key);

        if (empty($cached)) {
            return;
        }

        $data = unserialize($cached);
        if (is_array($data)) {
            $this->_data = $data;
        }
    }

    public function save(): void
    {
        if (!isset($this->cache)) {
            return;
        }

        $data = $this->_data;

        $str = serialize($data);
        $key = $this->getSessionKey(
            $this->session->getAppId(),
            $this->session->getSessionId()
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
        unset($this->_data);

        SpyAgency::decr(static::class);
    }


}