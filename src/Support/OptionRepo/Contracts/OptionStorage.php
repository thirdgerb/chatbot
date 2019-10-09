<?php


namespace Commune\Support\OptionRepo\Contracts;


use Commune\Support\Option;
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
     * @param Option $option
     * @param StorageMeta $meta
     */
    public function save(
        Option $option,
        StorageMeta $meta
    ) : void;

    public function get(
        string $optionName,
        string $id,
        StorageMeta $meta
    ) : ? Option;

    /**
     * 查看一个 option 是否定义过, 已经存在.
     * 通常由 root storage 来进行 has 检查.
     * 否则可能会导致管道层层向下.
     * 所以 root Storage 的 has 方法必须做到高性能.
     *
     * @param string $optionName
     * @param string $id
     * @param StorageMeta $meta
     * @return bool
     */
    public function has(
        string $optionName,
        string $id,
        StorageMeta $meta
    ) : bool;

    /**
     * @param string $optionName
     * @param StorageMeta $meta
     * @param string ...$ids
     */
    public function delete(string $optionName, StorageMeta $meta, string ...$ids) : void;

    /**
     * 锁定一个要存储的id. 锁定成功了可以去存. 避免抢占.
     *
     * @param string $optionName
     * @param string $id
     * @param StorageMeta $meta
     * @return bool
     */
    public function lockId(string $optionName, string $id, StorageMeta $meta) : bool;
}