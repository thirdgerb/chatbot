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

use Commune\Support\Registry\Meta\CategoryOption;
use Commune\Support\Option\Option;
use Commune\Support\Registry\Meta\StorageOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface StorageDriver
{

    /**
     * 初始化某个库.
     *
     * @param CategoryOption $categoryOption
     * @param StorageOption $storageOption
     */
    public function boot(
        CategoryOption $categoryOption,
        StorageOption $storageOption
    ) : void;

    /**
     * @param CategoryOption $categoryOption
     * @param StorageOption $storageOption
     * @param string $optionId
     * @return bool
     */
    public function has(
        CategoryOption $categoryOption,
        StorageOption $storageOption,
        string $optionId
    ) : bool;

    /**
     * @param CategoryOption $categoryOption
     * @param StorageOption $storageOption
     * @param string $optionId
     * @return Option
     */
    public function find(
        CategoryOption $categoryOption,
        StorageOption $storageOption,
        string $optionId
    ) : ? Option;

    public function count(
        CategoryOption $categoryOption,
        StorageOption $storageOption
    ) : int;

    /**
     * 更新, 或者保存一个 option.
     *
     * @param CategoryOption $categoryOption
     * @param StorageOption $storageOption
     * @param Option $option
     * @param bool $notExists
     * @return bool
     */
    public function save(
        CategoryOption $categoryOption,
        StorageOption $storageOption,
        Option $option,
        bool $notExists = false
    ) : bool;


    /**
     * 通过 ID 删除掉若干个 Option
     *
     * @param CategoryOption $categoryOption
     * @param StorageOption $storageOption
     * @param string $id
     * @param string[] $ids
     * @return int
     */
    public function delete(
        CategoryOption $categoryOption,
        StorageOption $storageOption,
        string $id,
        string ...$ids
    ) : int;

    /**
     * @param CategoryOption $categoryOption
     * @param StorageOption $storageOption
     * @return \Generator|Option[]
     */
    public function eachOption(
        CategoryOption $categoryOption,
        StorageOption $storageOption
    ) : \Generator;

    /**
     * 使用 id 数组, 取出相关 Option 的 map
     *
     * @param CategoryOption $categoryOption
     * @param StorageOption $storageOption
     * @param array $ids
     * @return array
     */
    public function findByIds(
        CategoryOption $categoryOption,
        StorageOption $storageOption,
        array $ids
    ) : array;

    /**
     * @param CategoryOption $categoryOption
     * @param StorageOption $storageOption
     * @param string $wildcardId
     * @return array
     */
    public function searchIds(
        CategoryOption $categoryOption,
        StorageOption $storageOption,
        string $wildcardId
    ) : array;

    /**
     * @param CategoryOption $categoryOption
     * @param StorageOption $storageOption
     * @return \Generator |string[]
     */
    public function eachId(
        CategoryOption $categoryOption,
        StorageOption $storageOption
    ) : \Generator;

    /**
     * @param CategoryOption $categoryOption
     * @param StorageOption $storageOption
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function paginateIds(
        CategoryOption $categoryOption,
        StorageOption $storageOption,
        int $offset = 0,
        int $limit = 20
    ) : array;

    /**
     * 清除所有的数据. 非常危险的操作.
     *
     * @param CategoryOption $categoryOption
     * @param StorageOption $storageOption
     * @return bool
     */
    public function flush(
        CategoryOption $categoryOption,
        StorageOption $storageOption
    ) : bool;
}