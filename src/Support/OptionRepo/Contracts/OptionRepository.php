<?php

namespace Commune\Support\OptionRepo\Contracts;

use Commune\Chatbot\Config\Options\OptionRepoMeta;
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
interface OptionRepo
{
    /*---- meta ----*/

    /**
     * 注册一个 option 仓库的元数据.
     * @param OptionRepoMeta $meta
     */
    public function registerMeta(OptionRepoMeta $meta) : void;

    /**
     * 获取一个 option 仓库的元数据. 不存在会抛出异常.
     * @param string $optionName
     * @return OptionRepoMeta
     * @throws RepositoryMetaNotExistsException
     */
    public function getMeta(string $optionName) :  OptionRepoMeta;

    /**
     * 仓库是否存在.
     * @param string $optionName
     * @return bool
     */
    public function hasMeta(string $optionName) : bool ;

    /*---- 单个 option 管理 ----*/

    /**
     * 检查 option 是否存在.
     * @param string $optionName
     * @param string $id
     * @param ContainerInterface $container
     * @throws RepositoryMetaNotExistsException
     * @return bool
     */
    public function has(string $optionName, string $id, ContainerInterface $container) : bool;

    /**
     * 获取一个 option
     * @param string $optionName
     * @param string $id
     * @param ContainerInterface $container
     * @return Option|null
     *
     * @throws RepositoryMetaNotExistsException
     * @throws OptionNotFoundException
     */
    public function find(string $optionName, string $id, ContainerInterface $container) : Option;

    /**
     * 获取一个 option 在所有 storage 中的版本.
     * @param string $optionName
     * @param string $id
     * @param ContainerInterface $container
     * @return Option[]
     */
    public function findEach(string $optionName, string $id, ContainerInterface $container) : array;

    /**
     * 更新, 或者创建一个 option. meta 必须存在.
     *
     * @param Option $option
     * @param bool $draft 如果是草稿, 则不会立刻同步.
     * @param ContainerInterface $container
     *
     * @throws RepositoryMetaNotExistsException
     * @throws OptionNotFoundException
     * @throws SynchroniseFailException
     */
    public function save(Option $option, bool $draft = false, ContainerInterface $container) : void;

    /**
     * 删除掉一个 option
     * @param string $optionName
     * @param string $id
     * @param ContainerInterface $container
     * @throws RepositoryMetaNotExistsException
     */
    public function delete(string $optionName, string $id, ContainerInterface $container) : void;

    /**
     * 强制同步一个 option
     * @param string $optionName
     * @param ContainerInterface $container
     * @param string $id
     *
     * @throws RepositoryMetaNotExistsException
     * @throws OptionNotFoundException
     * @throws SynchroniseFailException
     */
    public function sync(string $optionName, string $id, ContainerInterface $container) : void;



    /*---- 多个 option 管理 ----*/
    /**
     * 获取一种 option 存储的总数.
     * @param string $optionName
     * @param ContainerInterface $container
     * @return int
     */
    public function count(string $optionName, ContainerInterface $container) : int;

    /**
     * 分页列举一种 option 的 id => brief
     *
     * @param string $optionName
     * @param ContainerInterface $container
     * @param int $page
     * @param int $lines
     * @return string[]
     */
    public function paginateIdToBrief(string $optionName, ContainerInterface $container, int $page = 1, int $lines = 20) : array;


    /**
     * 取出所有 option 的ID
     *
     * @param string $optionName
     * @param ContainerInterface $container
     * @return array
     */
    public function getAllOptionIds(string $optionName, ContainerInterface $container) : array;


    /**
     * @param string $optionName
     * @param array $ids
     * @param ContainerInterface $container
     * @return Option[]
     */
    public function findOptionsByIds(string $optionName, array $ids, ContainerInterface $container) : array;


    /**
     * 使用关键词从 option brief 中搜索 option
     * query 是什么就不关心了. 由root storage 自身决定.
     *
     * @param string $optionName
     * @param string $query
     * @param ContainerInterface $container
     * @return Option[]
     */
    public function searchInBriefs(string $optionName, string $query, ContainerInterface $container) : array;

}