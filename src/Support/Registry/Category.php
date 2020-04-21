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

use Commune\Support\Registry\Exceptions\StructNotFoundException;
use Commune\Support\Option\Option;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Category
{

    /*---- 单个 struct 管理 ----*/

    /**
     * 检查 struct 是否存在.
     *
     * @param string $optionId          struct id
     * @return bool
     */
    public function has(string $optionId) : bool;

    /**
     * 获取一个 struct
     *
     * @param string $optionId          struct id
     * @return Option
     * @throws StructNotFoundException struct不存在也抛出异常
     */
    public function find(string $optionId) : Option;

    /**
     * 获取一个 struct 在所有 storage 中的版本.
     * 可以用于检查存储介质是否发生了不一致.
     *
     * @param string $optionId
     * @return Option[]
     */
    public function getStorageVersions(string $optionId) : array;

    /**
     * 更新, 或者创建一个 struct.
     * 会先存储到 root storage, 然后从上往下一层层存储.
     * meta 必须存在.
     *
     * @param Option $struct
     * @param bool $draft 如果是草稿, 则不会立刻同步.
     *
     * @throws StructNotFoundException
     */
    public function save(Option $struct, bool $draft = false) : void;


    /**
     * 更新, 或者创建一批 struct. meta 必须存在.
     *
     * @param bool $draft 是否立刻同步.
     * @param Option[] $structs
     */
    public function saveBatch(
        bool $draft,
        Option ...$structs
    ) : void;

    /**
     * 同步整个 category 所有的 struct
     * 遍历 root storage 的存储, 同步给所有 storage
     *
     * @param bool $rootToTop       从下往上还是从上往下
     */
    public function syncCategory(bool $rootToTop) : void;

    /**
     * 强制同步一个 struct
     * @param string $id
     *
     */
    public function syncOption(string $id) : void;


    /**
     * 通过 ID 删除掉若干个 struct
     *
     * @param string[] $ids
     */
    public function delete(string ...$ids) : void;



    /*---- 多个 struct 管理 ----*/

    /**
     * 获取一种 struct 存储的总数.
     * @return int
     */
    public function count() : int;

    /**
     * 取出所有 option 的ID
     * @return string[]
     */
    public function getAllIds() : array;


    /**
     * 分页列举 options
     *
     * @param string $query
     * @param int $offset
     * @param int $lines
     * @return Option[]
     */
    public function paginate(string $query = '', int $offset = 0, int $lines = 20) : array;

    /**
     * 使用 id 数组, 取出相关 struct 的 map
     *
     * @param array $ids
     * @return Option[]
     */
    public function findByIds(array $ids) : array;



    /**
     * 遍历一个 category 下所有的 struct 实例.
     *
     * @return \Generator
     */
    public function each() : \Generator;


}