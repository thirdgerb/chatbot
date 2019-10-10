<?php

namespace Commune\Support\OptionRepo\Contracts;

use Commune\Support\OptionRepo\Options\CategoryMeta;
use Commune\Support\Option;
use Commune\Support\OptionRepo\Exceptions\OptionNotFoundException;
use Commune\Support\OptionRepo\Exceptions\RepositoryMetaNotExistsException;
use Commune\Support\OptionRepo\Exceptions\SynchroniseFailException;
use Psr\Container\ContainerInterface;


/**
 * 配置的抽象层. 从不同的 option storage 介质中读取 option 对象.
 * 这样做的目的是实现一个可替换介质的分布式配置中心.
 * 可以一点修改配置, 多点更新配置.
 *
 * 如果系统的默认配置在 etcd, 则与 etcd 高度耦合. 没有 etcd 的场景甚至无法启动.
 * 一些开源项目可以把配置写在 yaml 中, 用仓库直接传播. 但却无法实现线上多服务器同步修改.
 *
 * 有一个抽象层, 则可以解决这种问题.
 *
 */
interface OptionRepository
{
    /*---- meta ----*/

    /**
     * 注册一个 option 仓库的元数据.
     * @param CategoryMeta $meta
     */
    public function registerCategory(CategoryMeta $meta) : void;

    /**
     * 获取一个 option 仓库的元数据. 不存在会抛出异常.
     * @param string $category
     * @return CategoryMeta
     * @throws RepositoryMetaNotExistsException
     */
    public function getCategoryMeta(string $category) :  CategoryMeta;

    /**
     * 仓库是否存在.
     * @param string $category
     * @return bool
     */
    public function hasCategory(string $category) : bool ;

    /*---- 单个 option 管理 ----*/

    /**
     * 检查 option 是否存在.
     * @param ContainerInterface $container
     * @param string $category
     * @param string $optionId
     * @throws RepositoryMetaNotExistsException
     * @return bool
     */
    public function has(
        ContainerInterface $container,
        string $category,
        string $optionId
    ) : bool;

    /**
     * 获取一个 option
     * @param ContainerInterface $container
     * @param string $category
     * @param string $optionId
     * @return Option|null
     *
     * @throws RepositoryMetaNotExistsException
     * @throws OptionNotFoundException
     */
    public function find(
        ContainerInterface $container,
        string $category,
        string $optionId
    ) : Option;

    /**
     * 获取一个 option 在所有 storage 中的版本.
     *
     * @param ContainerInterface $container
     * @param string $category
     * @param string $optionId
     * @return Option[]
     */
    public function findAllVersions(
        ContainerInterface $container,
        string $category,
        string $optionId
    ) : array;

    /**
     * 更新, 或者创建一个 option. meta 必须存在.
     *
     * @param ContainerInterface $container
     * @param string $category
     * @param Option $option
     * @param bool $draft 如果是草稿, 则不会立刻同步.
     *
     * @throws RepositoryMetaNotExistsException
     * @throws OptionNotFoundException
     * @throws SynchroniseFailException
     */
    public function save(
        ContainerInterface $container,
        string $category,
        Option $option,
        bool $draft = false
    ) : void;

    /**
     * 删除掉一个 option
     * @param ContainerInterface $container
     * @param string $category
     * @param string $id
     * @throws RepositoryMetaNotExistsException
     */
    public function delete(
        ContainerInterface $container,
        string $category,
        string $id
    ) : void;

    /**
     * 强制同步一个 option
     * @param ContainerInterface $container
     * @param string $category
     * @param string $id
     *
     * @throws RepositoryMetaNotExistsException
     * @throws OptionNotFoundException
     * @throws SynchroniseFailException
     */
    public function sync(
        ContainerInterface $container,
        string $category,
        string $id
    ) : void;



    /*---- 多个 option 管理 ----*/
    /**
     * 获取一种 option 存储的总数.
     * @param ContainerInterface $container
     * @param string $category
     * @return int
     */
    public function count(
        ContainerInterface $container,
        string $category
    ) : int;

    /**
     * 分页列举一种 option 的 id => brief
     *
     * @param ContainerInterface $container
     * @param string $category
     * @param int $page
     * @param int $lines
     * @return string[]
     */
    public function paginateIdToBrief(
        ContainerInterface $container,
        string $category,
        int $page = 1,
        int $lines = 20
    ) : array;


    /**
     * 取出所有 option 的ID
     *
     * @param ContainerInterface $container
     * @param string $category
     * @return array
     */
    public function getAllOptionIds(
        ContainerInterface $container,
        string $category
    ) : array;


    /**
     * @param ContainerInterface $container
     * @param string $category
     * @param array $ids
     * @return Option[]
     */
    public function findOptionsByIds(
        ContainerInterface $container,
        string $category,
        array $ids
    ) : array;


    /**
     * 使用关键词从 option brief 中搜索 option
     * query 是什么就不关心了. 由root storage 自身决定.
     *
     * @param ContainerInterface $container
     * @param string $category
     * @param string $query
     * @return Option[]
     */
    public function searchInBriefs(
        ContainerInterface $container,
        string $category,
        string $query
    ) : array;


    /**
     * 迭代一个category 下所有的option实例.
     *
     * @param ContainerInterface $container
     * @param string $category
     * @return \Generator
     */
    public function eachOption(
        ContainerInterface $container,
        string $category
    ) : \Generator;
}