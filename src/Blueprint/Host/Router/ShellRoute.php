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
 * # 可以根据 ShellName 来广播.
 *
 * @property-read string $id
 * @property-read string $shellName
 * @property-read string $shellId
 */
interface ShellRoute
{
    public function getRouteId() : string;

    public function getShellName() : string;

    public function getSessionId() : string;

    public function getConversationId() : string;

    public function newHub() : RouteHub;
}