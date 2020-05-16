<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Cloner;

use Commune\Blueprint\Framework\Session\SessionStorage;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ClonerStorage extends SessionStorage
{
    // 当前进程的存储
    const CURRENT_PROCESS_ID = 'currentProcessId';
    // 当前可以要读取的缓存 id
    const LAST_RECOLLECTION_IDS = 'lastRecollectionIds';

}