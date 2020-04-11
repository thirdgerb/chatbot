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

use Commune\Support\Registry\Meta\CategoryMeta;
use Commune\Support\Registry\Meta\StorageMeta;
use Commune\Support\Struct\Struct;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface RootStorage extends Storage
{

    /**
     * 获取一种 category 存储 option 的总数.
     *
     * @param CategoryMeta $category
     * @param StorageMeta $storage
     * @return int
     */
    public function count(CategoryMeta $category, StorageMeta $storage) : int;

    /**
     * 分页列举一种 option 的 id => brief
     * 最基本的分页. 没有排序逻辑. 需要复杂逻辑的请用 searchStructsByQuery 并自行实现query
     *
     * @param CategoryMeta $category
     * @param StorageMeta $storage
     * @param string $query
     * @param int $offset
     * @param int $lines
     * @return string[]
     */
    public function paginate(
        CategoryMeta $category,
        StorageMeta $storage,
        string $query = '',
        int $offset = 0,
        int $lines = 20
    ) : array;


    /**
     * 取出所有 option 的ID
     *
     * @param CategoryMeta $category
     * @param StorageMeta $storage
     * @return array
     */
    public function getAllIds(CategoryMeta $category, StorageMeta $storage) : array;


    /**
     * @param CategoryMeta $category
     * @param StorageMeta $storage
     * @param array $ids
     * @return Struct[]
     */
    public function findByIds(CategoryMeta $category, StorageMeta $storage, array $ids) : array;


    /**
     * 获取每一个.
     *
     * @param CategoryMeta $category
     * @param StorageMeta $storage
     * @return \Generator
     */
    public function each(CategoryMeta $category, StorageMeta $storage) : \Generator;

}