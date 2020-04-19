<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Cloner;

use Commune\Ghost\Blueprint\Convo\Conversation;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Cloner
{
    public function getCloneId() : string;

    public function getSessionId() : string;

    public function addShellId(string $shellId, string $shellName) : void;

    public function removeShellId(string $shellId) : void;

    public function getShellNameToIds() : array;

    /**
     * @return string[]   shellId => shellName
     */
    public function getShellIdToNames() : array;

    /*----- locker ------*/

    /**
     * 锁定一个机器人的分身. 禁止通讯.
     *
     * @param int $second
     * @return bool
     */
    public function lock(int $second) : bool;

    /**
     * 解锁一个机器人的分身. 允许通讯.
     * @return bool
     */
    public function unlock() : bool;

    /*----- save ------*/

    public function save(Conversation $conversation) : void;
}