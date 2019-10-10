<?php


namespace Commune\Support\OptionRepo\Contracts;


use Commune\Support\Option;
use Commune\Support\OptionRepo\Options\CategoryMeta;
use Commune\Support\OptionRepo\Options\StorageMeta;

/**
 * 读取 Option 的存取介质的定义.
 * OptionStorage 通常在 IoC 容器里生成.
 * 因此介质的实现必须先在容器里绑定.
 * 如果使用双容器策略, 则需要预先考虑OptionStorage 的实现绑定在哪个容器里.
 * 调用的时候能否拿到正确的容器.
 */
interface OptionStorage
{

    /**
     * 保存一个数据. 更新或者存储.
     *
     * @param CategoryMeta $category
     * @param StorageMeta $storage
     * @param Option $option
     */
    public function save(
        CategoryMeta $category,
        StorageMeta $storage,
        Option $option
    ) : void;

    /**
     * @param CategoryMeta $category
     * @param StorageMeta $storage
     * @param string $id
     * @return Option|null
     */
    public function get(
        CategoryMeta $category,
        StorageMeta $storage,
        string $id
    ) : ? Option;

    /**
     * 查看一个 option 是否定义过, 已经存在.
     * 通常由 root storage 来进行 has 检查.
     * 否则可能会导致管道层层向下.
     * 所以 root Storage 的 has 方法必须做到高性能.
     *
     * @param CategoryMeta $category
     * @param StorageMeta $storage
     * @param string $id
     * @return bool
     */
    public function has(
        CategoryMeta $category,
        StorageMeta $storage,
        string $id
    ) : bool;

    /**
     * @param CategoryMeta $category
     * @param StorageMeta $storage
     * @param string ...$ids
     */
    public function delete(
        CategoryMeta $category,
        StorageMeta $storage,
        string ...$ids
    ) : void;

    /**
     * 锁定一个要存储的id. 锁定成功了可以去存. 避免抢占.
     *
     * @param CategoryMeta $category
     * @param string $id
     * @param StorageMeta $storage
     * @return bool
     */
    public function lockId(
        CategoryMeta $category,
        StorageMeta $storage,
        string $id
    ) : bool;
}