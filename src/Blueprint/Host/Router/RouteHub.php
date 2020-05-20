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

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
interface RouteHub
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
     * @return GhostRoute
     */
    public function getGhostRoute() : GhostRoute;

    /**
     * @return ShellRoute[]
     */
    public function getShellRouteMap() : array;

    /**
     * @param ShellRoute $route
     * @return RouteHub
     */
    public function append(ShellRoute $route) : RouteHub;

    /**
     * @param ShellRoute $route
     * @return RouteHub
     */
    public function remove(ShellRoute $route) : RouteHub;




}