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

use Commune\Blueprint\Ghost\Tools\Deliver;


/**
 * 对话式的服务, 执行完后用对话来返回结果.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface DialogicService
{

    public function __invoke(array $payload, Deliver $deliver) : void;
}