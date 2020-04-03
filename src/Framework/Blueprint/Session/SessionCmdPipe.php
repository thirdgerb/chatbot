<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint\Session;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface SessionCmdPipe extends SessionPipe
{
    public function hasCommand(string $commandName) : bool;


    public function getCommandID(string $commandName) : string;

    public function getCommandMark() : string;

    public function getDescriptions() : array;

    public function getCommandDesc(string $commandName) : string;
}