<?php


namespace Commune\Chatbot\OOHost\Session;


use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\History\Yielding;

/**
 * Session 的数据读写驱动. 可以实现自己的策略.
 *
 * ## 关于 context 和GC
 *
 * 并不是所有的 context 都需要存储下来. 主要是 Memory 需要持久化存储.
 * 其它的则可以用缓存来临时存储.
 *
 * 这里又有一个 gc 的逻辑. 当一个 context 在上下文中不被人使用了, 就会触发 gcContext. 通常是删除掉它的缓存.
 *
 */
interface Driver extends RunningSpy
{

    /*------- snapshot -------*/

    /**
     * 保存 snapshot
     * @param Snapshot $snapshot
     * @param int $expireSeconds
     */
    public function saveSnapshot(Snapshot $snapshot, int $expireSeconds = 0) : void;

    public function findSnapshot(string $sessionId, string $belongsTo) : ? Snapshot;

    public function clearSnapshot(string $sessionId, string $belongsTo) : void;

    /*------- yielding -------*/

    public function saveYielding(Session $session, Yielding $yielding) : void;

    public function findYielding(string $contextId) : ? Yielding;



    /*------- context -------*/

    public function saveContext(Session $session, Context $context) : void;

    public function findContext(Session $session, string $contextId) : ? Context;


    /*------- gc -------*/

    /**
     * 记录 session 的 gc 计数器.
     * 用于记录 context 相互持有的情况.
     * 当一个 context 的 gc 计数器为 0 时,
     * 如果它已经不在任何一个 snapshot 中持有, 就会被gc
     *
     * @param string $sessionId
     * @return array
     */
    public function getGcCounts(string $sessionId) : array;

    public function saveGcCounts(string $sessionId, array $counts) : void;


    /**
     * 这些 context ids 不再被上下文所持有, 也没有相互持有, 于是可以清除了.
     * 通常是清除掉缓存. 这里面不包括 memory
     *
     * @param Session $session
     * @param string ...$ids
     */
    public function gcContexts(Session $session, string ...$ids) : void;
}