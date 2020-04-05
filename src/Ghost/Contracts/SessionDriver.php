<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Contracts;

use Commune\Framework\Blueprint\Intercom\GhostInput;
use Commune\Ghost\Blueprint\Chat\ChatScope;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface SessionDriver
{
    /*----- chat scope -----*/

    public function findScope(string $shellChatId) : ? ChatScope;

    public function saveScope(string $shellChatId, ChatScope $scope) : void;

    /*----- message -----*/

    public function recordInputMessage(
        GhostInput $input,
        ChatScope $scope
    ) : void;

    public function paginateMessages(
        int $offset = 0,
        int $limit = 10
    ) : array;

}