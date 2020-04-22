<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Host;

use Commune\Blueprint\Host\Router\IntercomRoute;

/**
 * 通信路由表.
 *
 * 思路是:
 * - Shell 通信的时候要知道 CloneId, 可以主动推送给目标 CloneId.
 * - Clone 回传消息的时候, 要知道 ShellNames, 在广播的时候可以广播给指定的 Shell.
 *
 * Shell 拿到消息就有以下几种策略:
 *
 * 1. 同步请求, 同步拿到本次响应. 无法接受广播.
 * 1. 同步请求, 主动拉取最新信息, 从收件箱获取. Session 要保留最后收件 Id.
 * 1. 监听 Shell 端广播, 只拿到跟 Shell 有过通讯的消息, 并且能感知投递 shellId. 例如钉钉.
 * 1. 监听 Ghost 广播, 全量广播, 可以拿到所有通话消息. (广播也可以通过队列来做)
 *
 * 所需要的路由:
 *
 * 1. Shell 方要能感知到推送的目标 CloneId.
 * 1. Shell 方要从推送消息感知到自己的 ShellId.
 * 1. Ghost 平台要能从 CloneId 感知到需要推送的 ShellName.
 *
 * 通讯作废情况:
 *
 * 1. Clone 的 Session 关闭了. 接受通知方都应该关闭 Session.
 * 1. Shell 的 Session 关闭了. Clone 方理论上还要继续.
 *
 * 主动设置:
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
interface Router
{
    /**
     * 从 Shell 端主动找到路由.
     *
     * @param string $shellName
     * @param string $shellId
     * @param string $sessionId
     * @param bool $stateless
     * @return IntercomRoute|null    无状态请求不会消耗内存.
     */
    public function shellFindRoute(
        string $shellName,
        string $shellId,
        string $sessionId,
        bool $stateless = false
    ) : ? IntercomRoute;

    /**
     * 从 Shell 端主动找到路由, 否则生成一个并保存.
     *
     * @param string $shellName
     * @param string $shellId
     * @param string $sessionId
     * @param bool $stateless
     * @return IntercomRoute|null    无状态请求不会消耗内存.
     */
    public function shellFindOrCreateRoute(
        string $shellName,
        string $shellId,
        string $sessionId,
        bool $stateless = false
    ) : IntercomRoute;

    /**
     * 用 Clone 主动找到路由.
     *
     * @param string $cloneId
     * @param string $sessionId
     * @return IntercomRoute
     */
    public function ghostFindRoute(
        string $cloneId,
        string $sessionId
    ) : IntercomRoute;

    /**
     * 保存一个路由. 数据只存一份, 但 ID 存 1+n 个位置:
     * - hash($cloneRoute->id)
     * + hash($shellRoute->id)
     *
     * 每次保存时延期. 两端都可以操作. 通常是 Ghost 主动续期.
     *
     * @param IntercomRoute $route
     * @param int $expire               通常和 Session 保持一致.
     */
    public function save(IntercomRoute $route, int $expire) : void;

    /**
     * 去掉所有位置存储的路由.
     * @param IntercomRoute $route
     */
    public function forget(IntercomRoute $route) : void;

    /**
     * 检查现存的有状态对话数量.
     * @return int
     */
    public function countRoutes() : int;
}