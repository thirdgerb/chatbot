<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\FPHost\Blueprint\Session;


/**
 * 能够在 Session 中进行存取的数据. 被其它对象持有时, 可只持有 SessionDataIdentity
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface SessionData
{
    public function toSessionDataId() : SessionDataId;
}