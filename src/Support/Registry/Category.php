<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Registry;

use Commune\Support\Registry\Exceptions\OptionNotFoundException;
use Commune\Support\Option\Option;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Category
{

    /**
     * 初始化 Storage.
     *
     * @param bool $initialize  会将 initial Storage 的数据尝试加载到 Storage
     */
    public function boot(bool $initialize = false) : void;

    /*---- 单个 Option 管理 ----*/

    /**
     * 检查 Option 是否存在.
     *
     * @param string $optionId          Option id
     * @return bool
     */
    public function has(string $optionId) : bool;

    /**
     * 获取一个 Option
     *
     * @param string $optionId          Option id
     * @return Option
     * @throws OptionNotFoundException Option不存在也抛出异常
     */
    public function find(string $optionId) : Option;

    /**
     * 更新, 或者保存一个 option.
     *
     * @param Option $option
     * @param bool $notExists   只有在目标不存在时才能存储.
     * @return bool
     */
    public function save(Option $option, bool $notExists = false) : bool;


    /**
     * 通过 ID 删除掉若干个 Option
     *
     * @param string $id
     * @param string ...$ids
     * @return int              删除数量.
     */
    public function delete(string $id, string ...$ids) : int;


    /**
     * 使用 id 数组, 取出相关 Option 的 map
     *
     * @param array $ids
     * @return Option[]
     */
    public function findByIds(array $ids) : array;


    /*---- 多个 Option 管理 ----*/

    /**
     * 获取一种 Option 存储的总数.
     * @return int
     */
    public function count() : int;

    /**
     * 取出所有 option 的ID
     * @return string[]
     */
    public function getAllIds() : array;


    /**
     * 分页列举 options, 自然排序
     *
     * @param int $offset
     * @param int $limit
     * @return Option[]
     */
    public function paginate(int $offset = 0, int $limit = 20) : array;

    /**
     * 用通配符查找可能的 id
     * @param string $wildcardId
     * @return string[]
     */
    public function searchIds(string $wildcardId) : array;

    /**
     * 用通配符计算出匹配的数量.
     * @param string $wildcardId
     * @return int
     */
    public function searchIdExists(string $wildcardId) : int;

    /**
     * 遍历一个 category 下所有的 Option 实例.
     *
     * @return \Generator
     */
    public function each() : \Generator;

    /*---- storage ----*/

    public function getStorage() : Storage;

    public function getInitialStorage() : ? Storage;
}