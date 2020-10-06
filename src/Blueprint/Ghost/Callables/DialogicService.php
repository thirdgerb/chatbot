<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Callables;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;


/**
 * 对话式的服务, 执行完后用对话来返回结果.
 * 可以同步运行, 也可以用一个对话子进程异步运行
 * 异步运行的方式是
 * Cloner->dispatcher->asyncService()
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface DialogicService
{
    public function policies() : array;

    public function __invoke(Dialog $dialog, array $payload) : ? Operator;
}