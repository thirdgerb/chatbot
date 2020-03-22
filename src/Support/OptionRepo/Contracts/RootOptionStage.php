<?php


namespace Commune\Support\OptionRepo\Contracts;


use Commune\Support\Struct;
use Commune\Support\OptionRepo\Options\CategoryMeta;
use Commune\Support\OptionRepo\Options\StorageMeta;

/**
 * 根 storage 负责基础数据的存取.
 * 一般而言 category 只需要根 storage 就够了.
 * 但有时根 storage 性能不够好, 或者做不到分布式一致, 或者不方便修改
 * 这时就需要 storagePipeline 做为缓存层
 *
 * 根 storage 要掌握一些额外的能力.
 */
interface RootOptionStage extends OptionStorage
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
     * 最基本的分页. 没有排序逻辑. 需要复杂逻辑的请用 searchOptionsByQuery 并自行实现query
     *
     * @param CategoryMeta $category
     * @param StorageMeta $storage
     * @param int $page
     * @param int $lines
     * @return string[]
     */
    public function paginateIdToBrief(CategoryMeta $category, StorageMeta $storage, int $page = 1, int $lines = 20) : array;


    /**
     * 取出所有 option 的ID
     *
     * @param CategoryMeta $category
     * @param StorageMeta $storage
     * @return array
     */
    public function getAllOptionIds(CategoryMeta $category, StorageMeta $storage) : array;


    /**
     * @param CategoryMeta $category
     * @param StorageMeta $storage
     * @param array $ids
     * @return Option[]
     */
    public function findOptionsByIds(CategoryMeta $category, StorageMeta $storage, array $ids) : array;


    /**
     * 使用关键词从搜索 option
     * query 是什么就不关心了. 由root storage 自身决定.
     *
     * @param CategoryMeta $category
     * @param StorageMeta $storage
     * @param string $query
     * @return Option[]
     */
    public function searchOptionsByQuery(CategoryMeta $category, StorageMeta $storage, string $query) : array;


    /**
     * 获取每一个.
     *
     * @param CategoryMeta $category
     * @param StorageMeta $storage
     * @return \Generator
     */
    public function eachOption(CategoryMeta $category, StorageMeta $storage) : \Generator;
}