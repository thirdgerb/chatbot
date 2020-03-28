<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Prototype;

use Commune\Support\Babel\Babel;
use PHPUnit\Framework\TestCase;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class MessagesTestCase extends TestCase
{
    abstract public function getMessages() : array;

    protected function tearDown()
    {
        Babel::setResolver(null);
    }

    public function testSerializable()
    {
        $messages = $this->getMessages();
        foreach ($messages as $messageName) {
            $msg = new $messageName;
            $s = Babel::getResolver()->serialize($msg);
            $msg1 = Babel::getResolver()->unSerialize($s);

            $this->assertEquals($msg->toArray(), $msg1->toArray());
            $this->assertEquals($msg->isEmpty(), $msg1->isEmpty());
        }
    }
}