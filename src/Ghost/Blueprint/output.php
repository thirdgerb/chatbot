<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint;

use Commune\Message\Blueprint\Reaction;


/**
 * 输出 Reaction 的单元.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface output
{

    /**
     * 发送同步响应. 同步响应一定能够发送出去.
     * @param Reaction $reaction
     */
    public function reply(Reaction $reaction) : void;

    /**
     * @param string $chatId
     * @param Reaction $reaction
     * @param array $shellNames
     */
    public function broadcast(
        string $chatId,
        Reaction $reaction,
        array $shellNames = []
    ) : void;

}