<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Message;

use Commune\Message\IHostMsg;
use Commune\Support\Message\MessageTestCase;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class MessagesTest extends MessageTestCase
{
    protected $messages = [
        IHostMsg::class,
    ];


}