<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Host\Router;

use Commune\Protocals\Intercom\GhostInput;
use Commune\Protocals\Intercom\ShellInput;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * # 路由关系
 * @property-read string $id                    路由自身的 Id
 * @property-read GhostRoute $ghostRoute
 * @property-read ShellRoute[] $shellRoutes
 */
interface IntercomRoute
{

    /**
     * 和 Route 的内容保持一致.
     * 如果 Route 值更改了, 所以在 Route 中的 Shell 都能更新缓存ID
     * 这样其它已经被踢掉的 Shell 就因为找不到 Route, 无法连进来了.
     *
     * @return string
     */
    public function getId() : string;

    /**
     * Route 实例的 Id 随着内容变化而更新
     * 如果两个 ID 不一样, 应该把上一个删了.
     * @return string
     */
    public function getLastId() : string;

    /**
     * Shell 生成 GhostMsg 用于发送.
     *
     * @param ShellInput $input
     * @return GhostInput
     */
    public function parse(ShellInput $input) : GhostInput;

    /**
     * 添加一个 Shell.
     * @param string $shellName
     * @param string $shellId
     * @param string $sessionId
     */
    public function append(string $shellName, string $shellId, string $sessionId) : void;

    /**
     * 去掉一个 Shell.
     * @param string $shellName
     * @param string $shellId
     * @return IntercomRoute
     */
    public function remove(string $shellName, string $shellId) : IntercomRoute;
}