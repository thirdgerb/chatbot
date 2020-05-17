<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Abstracted;


/**
 * 将消息理解成一个命令语句.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
interface Cmd
{
    public function setCmdStr(string $command) : void;

    public function hasCmdStr() : bool;

    public function getCmdStr() : ? string;

    public function getCmdName() : ? string;
}