<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Convo;

use Commune\Blueprint\Framework\Session\Storage;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ConvoStorage extends Storage
{
    // 当前进程的存储
    const CURRENT_PROCESS_ID = 'currentProcessId';

}