<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Contracts;

use Psr\SimpleCache\CacheInterface;

/**
 * 机器人系统公用的 Cache. 理论上 Shell 与 Ghost 共用的.
 * 所有的 key 都应该加入 Hostname 相关的前缀.
 * 以保证机器人共用资源之间不冲突.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Cache
{

    public function getPSR16Cache() : CacheInterface;

    /*------- key val --------*/

    /**
     * @param string $key
     * @param string $value
     * @param int|null $ttl 单位是秒
     * @return bool
     */
    public function set(string $key, string $value, int $ttl = null) : bool;

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key) : bool ;

    /**
     * @param string $key
     * @return string | null
     */
    public function get(string $key) : ? string;

    /*------- multi --------*/

    /**
     * @param array $keys
     * @param array|null $default
     * @return array
     */
    public function getMultiple(array $keys, array $default = null) : array;

    /**
     * @param array $values
     * @param int|null $ttl
     * @return bool
     */
    public function setMultiple(array $values, int $ttl = null) : bool;

    /*------- expire --------*/

    /**
     * 为一个 key 设定过期的时间.
     *
     * @param string $key
     * @param int $ttl
     * @return bool
     */
    public function expire(string $key, int $ttl) : bool;

    /**
     * 解开分布式锁
     * @param string $key
     * @return bool
     */
    public function forget(string $key) : bool;


    /**
     * @param array $keys
     * @return bool
     */
    public function delMultiple(array $keys) : bool;

    /*------- locker --------*/

    /**
     * 分布式的锁
     *
     * @param string $key
     * @param int|null $ttl
     * @return bool
     */
    public function lock(string $key, int $ttl = null) : bool;

    /**
     * @param string $key
     * @return bool
     */
    public function unlock(string $key) : bool;


    /*------- hash map. 不支持的话改用 multi 就可以 --------*/


    /**
     * @param string $key
     * @param string $memberKey
     * @param string $value
     * @param int|null $ttl
     * @return bool
     */
    public function hSet(string $key, string $memberKey, string $value, int $ttl =null ) : bool;

    /**
     * @param string $key
     * @param array $values
     * @param int|null $ttl
     * @return bool
     */
    public function hMSet(string $key, array $values, int $ttl = null) : bool;


    /**
     * @param string $key
     * @param array $memberKeys
     * @return array
     */
    public function hMGet(string $key, array $memberKeys) : array;

    /**
     * @param string $key
     * @param string $memberKey
     * @return null|string
     */
    public function hGet(string $key, string $memberKey) : ? string;

    /**
     * @param string $key
     * @return array
     */
    public function hGetAll(string $key) : array;

}