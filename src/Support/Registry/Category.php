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

use Commune\Support\Option\Option;
use Commune\Support\Registry\Exceptions\OptionNotFoundException;
use Commune\Support\Registry\Meta\CategoryOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Category
{

    /**
     * 当前分类的配置.
     * @return CategoryOption
     */
    public function getConfig() : CategoryOption;

    /**
     * 初始化 Storage.
     *
     * @param bool $initialize  会将 initial Storage 的数据尝试加载到 Storage
     */
    public function boot(bool $initialize = true) : void;

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
     * @param string[] $ids
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

    /**
     * 同步 initialStorage 的数据到 Storage
     */
    public function initialize() : void;

    /**
     * 删除所有的数据.
     *
     * @param bool $flushInitStorage  是否连 initStorage 也不放过.
     * @return bool
     */
    public function flush(bool $flushInitStorage = false) : bool;

    /**
     * 同步 storage 所有数据到 initStorage
     */
    public function syncStorage() : void;

    /*---- 多个 Option 管理 ----*/

    /**
     * 获取一种 Option 存储的总数.
     * @return int
     */
    public function count() : int;

    /**
     * 分页列举 options, 自然排序
     *
     * @param int $offset
     * @param int $limit
     * @return Option[]
     */
    public function paginate(int $offset = 0, int $limit = 20) : array;


    /**
     * @param int $offset
     * @param int $limit
     * @return string[]
     */
    public function paginateId(int $offset = 0, int $limit = 20) : array;

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
     * @return \Generator|string
     */
    public function eachId() : \Generator;

    /**
     * @return \Generator|Option[]
     */
    public function eachOption() : \Generator;


    /**
     * @return Storage
     */
    public function getStorage() : Storage;

    /**
     * @return Storage|null
     */
    public function getInitialStorage() : ? Storage;
}