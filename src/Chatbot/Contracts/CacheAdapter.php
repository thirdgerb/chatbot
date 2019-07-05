<?php

/**
 * Class CacheAdapter
 * @package Commune\Chatbot\Contracts
 */

namespace Commune\Chatbot\Contracts;

use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Commune\Chatbot\Blueprint\Exceptions\RuntimeExceptionInterface;

/**
 * 默认是 conversation 的组件.
 * 系统公用的 Cache.
 *
 * Interface CacheAdapter
 * @package Commune\Chatbot\Contracts
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface CacheAdapter extends RunningSpy
{

    /**
     * @param string $key
     * @param string $value
     * @param int $ttl 单位是秒
     * @return bool
     * @throws RuntimeExceptionInterface
     */
    public function set(string $key, string $value, int $ttl) : bool;

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key) : bool ;

    /**
     * @param string $key
     * @return string | null
     * @throws RuntimeExceptionInterface
     */
    public function get(string $key) : ? string;


    /**
     * 分布式的锁
     *
     * @param string $key
     * @param int|null $ttl
     * @return bool
     * @throws RuntimeExceptionInterface
     */
    public function lock(string $key, int $ttl = null) : bool;

    /**
     * @param string $key
     * @return bool
     */
    public function unlock(string $key) : bool;

    /**
     * 解开分布式锁
     * @param string $key
     * @return bool
     */
    public function forget(string $key) : bool;

}