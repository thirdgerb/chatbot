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
     *
     * @param Snapshot $snapshot
     * @param int $expireSeconds
     */
    public function saveSnapshot(Snapshot $snapshot, int $expireSeconds = 0) : void;

    /**
     * 通过 belongsTo 获取一个 Snapshot
     * @param string $sessionId
     * @param string $belongsTo
     * @return Snapshot|null
     */
    public function findSnapshot(string $sessionId, string $belongsTo) : ? Snapshot;

    /**
     * 清除掉一个 Snapshot
     * @param string $sessionId
     * @param string $belongsTo
     */
    public function clearSnapshot(string $sessionId, string $belongsTo) : void;

    /*------- yielding -------*/

    /**
     * @param Session $session
     * @param Yielding $yielding
     */
    public function saveYielding(Session $session, Yielding $yielding) : void;

    /**
     * @param string $contextId
     * @return Yielding|null
     */
    public function findYielding(string $contextId) : ? Yielding;



    /*------- context -------*/

    /**
     * 保存一个 Context 数据. Memory 必须持久话, 其它数据可以考虑存储在缓存中.
     *
     * @param Session $session
     * @param Context $context
     */
    public function saveContext(Session $session, Context $context) : void;

    /**
     * 根据 id 获取一个 Context 对象.
     *
     * @param Session $session
     * @param string $contextId
     * @return Context|null
     */
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

    /**
     * @param string $sessionId
     * @param array $counts
     */
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