<?php


namespace Commune\Chatbot\App\Drivers\Demo;

use Commune\Chatbot\Contracts\CacheAdapter;
use Psr\Log\LoggerInterface;

/**
 * 模拟的cache. 方便测试用.
 */
class ArrayCache implements CacheAdapter
{
    protected static $cached = [];

    protected $logger;

    /**
     * ArrayCache constructor.
     * @param LoggerInterface $Logger
     */
    public function __construct(LoggerInterface $Logger)
    {
        $this->logger = $Logger;
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
        unset(self::$cached[$key]);
        return true;
    }

    public function __destruct()
    {
        $this->logger->debug(__METHOD__);
    }

}