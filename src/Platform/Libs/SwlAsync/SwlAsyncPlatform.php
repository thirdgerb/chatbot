<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Libs\SwlAsync;

use Commune\Blueprint\Platform;
use Swoole\Server;
use Swoole\Table;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface SwlAsyncPlatform extends Platform
{
    public function getServer() : Server;

    public function getServerTable() : Table;

    public function getSessionFd(string $sessionId) :  ? int;

    public function getFdSession(int $fd) : ? string;

    public function isSessionExists(string $sessionId) : bool;

    public function setSessionRoute(string $sessionId, int $fd) : void;

    public function unsetSessionRoute(string $sessionId) : void;


}