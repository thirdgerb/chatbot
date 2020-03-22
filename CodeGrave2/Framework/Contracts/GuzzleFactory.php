<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Contracts;

use GuzzleHttp\Client;

/**
 * Guzzle Client 的 factory
 * 虽然 http 请求确定用 guzzle 做客户端
 * 但由于使用协程, 连接池等原因的考虑, 还是用一个Factory, 增加一层抽象
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GuzzleFactory
{
    public function create(array $config) : Client;
}

