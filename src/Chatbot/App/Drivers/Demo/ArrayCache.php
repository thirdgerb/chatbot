<?php


namespace Commune\Chatbot\App\Drivers\Demo;

use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Contracts\CacheAdapter;
use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;

/**
 * 模拟的cache. 方便测试用.
 */
class ArrayCache implements CacheAdapter
{
    use RunningSpyTrait;

    protected static $cached = [];

    protected $logger;

    /**
     * @var string
     */
    protected $traceId;

    public function __construct(Conversation $conversation)
    {
        $this->logger = $conversation->getLogger();
        $this->traceId = $conversation->getTraceId();
        self::addRunningTrace($this->traceId, $this->traceId);
    }


    public function set(string $key, string $value, int $ttl): bool
    {
        self::$cached[$key] = $value;
        return true;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, self::$cached);
    }

    public function get(string $key): ? string
    {
        return self::$cached[$key] ?? null;
    }


    public function lock(string $key, int $ttl = null): bool
    {
        return true;
    }

    public function forget(string $key): bool
    {
        $i = isset(self::$cached[$key]);
        unset(self::$cached[$key]);
        return $i;
    }

    public function unlock(string $key): bool
    {
        return $this->forget($key);
    }


    public function __destruct()
    {
        self::removeRunningTrace($this->traceId);
    }

}