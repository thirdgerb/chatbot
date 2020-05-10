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

use Commune\Message;
use Commune\Support\Message\MessageTestCase;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class MessagesTest extends MessageTestCase
{
    protected $messages = [
        // convo
        Message\Host\Convo\IVerbalMsg::class,
        Message\Host\Convo\IUnsupportedMsg::class,
        Message\Host\Convo\IEventMsg::class,
        Message\Host\Convo\IAudioMsg::class,
        Message\Host\Convo\IImageMsg::class,
        Message\Host\Convo\IContextMsg::class,

        // intent
        Message\Host\IIntentMsg::class,


        // intercom
        Message\Intercom\IShellInput::class,
        Message\Intercom\IShellOutput::class,


    ];


}