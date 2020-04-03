<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Chat;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read ChatScope $scope
 */
interface Chat
{
    public function getId() : string;

    public function lock() : bool;

    public function unlock() : bool;

    public function setScope(ChatScope $scope) : void;

}