<?php


namespace Commune\Chatbot\OOHost\NLU\Contracts;


use Commune\Support\Option;

/**
 * option 的管理者.
 */
interface Manager
{
    /**
     *
     * @param bool $force
     * @return string 为空表示成功, 否则输出错误信息.
     */
    public function sync(bool $force = false) : string;

    /**
     * 清空已有的记录.
     */
    public function flush() : void;

    /**
     * 持有的 Option 对象数量.
     * @return int
     */
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

    /**
     * 尝试通过 ID 获取一个配置
     * 没有的话, 应该初始化一个新的配置.
     *
     * @param string $id
     * @return Option
     */
    public function get(string $id) : Option;

    /**
     * 获取所有 Option 的 id 数组
     * @return array
     */
    public function getAllIds() : array;

    /**
     * 遍历 所有的 Option
     * @return \Generator
     */
    public function each() : \Generator;

    /**
     * 删除一个 Option
     * @param string $id
     */
    public function remove(string $id) : void;

    /**
     * 通过 ID, 获取一个 id => option 的 array
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
     *
     * @param Option $option
     * @return bool
     */
    public function register(Option $option) : bool;
}