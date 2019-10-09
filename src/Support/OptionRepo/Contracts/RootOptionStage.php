<?php


namespace Commune\Support\OptionRepo\Contracts;


use Commune\Support\Option;
use Commune\Support\OptionRepo\Options\StorageMeta;

/**
 * 根 storage 要掌握一些额外的能力.
 */
interface RootOptionStage extends OptionStorage
{

    /**
     * 获取一种 option 存储的总数.
     * @param string $optionName
     * @param StorageMeta $meta
     * @return int
     */
    public function count(string $optionName, StorageMeta $meta) : int;

    /**
     * 分页列举一种 option 的 id => brief
     * 最基本的分页. 没有排序逻辑. 需要复杂逻辑的请用 searchOptionsByQuery 并自行实现query
     *
     * @param string $optionName
     * @param StorageMeta $meta
     * @param int $page
     * @param int $lines
     * @return string[]
     */
    public function paginateIdToBrief(string $optionName, StorageMeta $meta, int $page = 1, int $lines = 20) : array;


    /**
     * 取出所有 option 的ID
     *
     * @param string $optionName
     * @param StorageMeta $meta
     * @return array
     */
    public function getAllOptionIds(string $optionName, StorageMeta $meta) : array;


    /**
     * @param string $optionName
     * @param array $ids
     * @param StorageMeta $meta
     * @return Option[]
     */
    public function findOptionsByIds(string $optionName, array $ids, StorageMeta $meta) : array;


    /**
     * 使用关键词从搜索 option
     * query 是什么就不关心了. 由root storage 自身决定.
     *
     * @param string $optionName
     * @param string $query
     * @param StorageMeta $meta
     * @return Option[]
     */
    public function searchOptionsByQuery(string $optionName, string $query, StorageMeta $meta) : array;


    /**
     * 获取每一个.
     *
     * @param string $optionName
     * @param StorageMeta $meta
     * @return \Generator
     */
    public function eachOption(string $optionName, StorageMeta $meta) : \Generator;
}