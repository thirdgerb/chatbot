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
interface Storage
{

    /**
     * 彻底清空一组缓存.
     *
     * @param CategoryMeta $category
     * @param StorageMeta $storage
     */
    public function flush(
        CategoryMeta $category,
        StorageMeta $storage
    ) : void;

    /**
     * 保存一个数据. 更新或者存储.
     *
     * @param CategoryMeta $category
     * @param StorageMeta $storage
     * @param Struct[] $options
     */
    public function save(
        CategoryMeta $category,
        StorageMeta $storage,
        Struct ...$options
    ) : void;

    /**
     * @param CategoryMeta $category
     * @param StorageMeta $storage
     * @param string $id
     * @return Struct|null
     */
    public function get(
        CategoryMeta $category,
        StorageMeta $storage,
        string $id
    ) : ? Struct;

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
     * @param string[] $ids
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