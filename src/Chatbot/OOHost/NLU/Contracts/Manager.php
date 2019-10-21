<?php


namespace Commune\Chatbot\OOHost\NLU\Contracts;


use Commune\Support\Option;

/**
 * option 的管理者.
 */
interface Manager
{
    /**
     * @param bool $force
     * @return string 为空表示成功, 否则输出错误信息.
     */
    public function sync(bool $force = false) : string;

    /**
     * 清空已有的记录.
     */
    public function flush() : void;

    public function count() : int;

    /**
     * 存在某个配置.
     * @param string $id
     * @return bool
     */
    public function has(string $id) : bool;

    /**
     * 在 option repository 中也存在同样的配置.
     * @param string $id
     * @return bool
     */
    public function hasSynced(string $id) : bool;

    public function get(string $id) : Option;

    public function getAllIds() : array;

    public function each() : \Generator;

    public function remove(string $id) : void;

    /**
     * @param string ...$ids
     * @return Option[]
     */
    public function getMap(string ...$ids) : array;

    /**
     * 保存一个option
     *
     * @param Option $option
     * @return string 返回错误说明, 为空表示成功.
     */
    public function save(Option $option) : string;

    /**
     * 注册一个 option, 只有 option 并不存在的时候, 才会注册成功.
     * @param Option $option
     * @return bool
     */
    public function register(Option $option) : bool;
}