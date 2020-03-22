<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Context;

use Commune\Ghost\Blueprint\Routing\Route;
use Commune\Ghost\Blueprint\Stage;

/**
 * 回调时的逻辑.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface CallbackStage extends Stage
{
    /**
     * 将当前 Thread 添加到 User 进程
     * @return Route
     */
    public function retain() : Route;

    /**
     * 继续等待回调
     * @return Route
     */
    public function await() : Route;

    /**
     * 放弃当前 Thread 任务
     * @return Route
     */
    public function drop() : Route;

}