<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Blueprint\Message;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ReplyMsg extends Message
{

    /*----- default reply ids -----*/

    # 退出会话
    const QUIT = 'replies.default.quit';
    # 无法理解
    const CONFUSED = 'replies.default.confused';
    # 拒绝访问
    const REJECTED = 'replies.default.rejected';
}