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