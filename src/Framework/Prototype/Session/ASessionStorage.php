<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Prototype\Session;

use Commune\Framework\Blueprint\Session\Session;
use Commune\Framework\Blueprint\Session\SessionStorage;
use Commune\Framework\Contracts\Cache;
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
    protected $chatId;

    /**
     * @var string
     */
    protected $traceId;

    /**
     * @var Cache|null
     */
    protected $cache = null;

    /*--- cached ---*/

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var int|null
     */
    protected $expire;

    /**
     * @var bool
     */
    protected $changed = false;

    /**
     * @var string
     */
    protected $cacheKey;


    public function __construct(
        Session $session,
        int $expire
    )
    {
        $this->chatId = $session->getChatId();
        $this->expire = $expire > 0 ? $expire : null;
        $this->cacheKey = $this->makeSessionCacheKey($session);

        if (!$session->isStateless()) {
            $this->cache = $session->getContainer()->make(Cache::class);
            $dataVal = $this->cache->get($this->cacheKey);
            if (!empty($dataVal)) {
                $decoded = json_decode($dataVal, true);
                if (is_array($decoded)) {
                    $this->setAll($decoded);
                }
            }
        }

        $this->traceId = $session->getTraceId();
        static::addRunningTrace($this->traceId, $this->traceId);
    }

    abstract protected function makeSessionCacheKey(Session $session) : string;

    public function get(string $name)
    {
        return $this->data[$name] ?? null;
    }

    public function set(string $name, $value): void
    {
        $this->data[$name] = $value;
        $this->changed = true;
    }

    public function setAll(array $values): void
    {
        $this->data = $values;
        $this->changed = true;
    }

    public function getAll(): array
    {
        return $this->data;
    }

    public function save(): void
    {
        // 如果 stateless
        if (!isset($this->cache)) {
            return;
        }

        // 数据有修改过
        if ($this->changed) {
            $this->cache->set($this->cacheKey, json_encode($this->data), $this->expire);

            // 如果 Session 有过期时间
        } elseif ($this->expire > 0 ) {
            $this->cache->expire($this->cacheKey, $this->expire);
        }
    }

    public function __destruct()
    {
        static::removeRunningTrace($this->traceId);
    }


}