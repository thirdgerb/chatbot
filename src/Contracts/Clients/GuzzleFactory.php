<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Contracts\Clients;

use GuzzleHttp\Client;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GuzzleFactory
{

    public function getClient() : Client;

}