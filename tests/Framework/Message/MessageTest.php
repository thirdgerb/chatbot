<?php

/**
 * Class MessageTest
 * @package Commune\Chatbot\Test\Framework\Message
 */

namespace Commune\Chatbot\Test\Framework\Message;


use Commune\Chatbot\Framework\Message\Message;
use Commune\Chatbot\Framework\Message\Text;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;

class MessageTest extends TestCase
{

    public function testSleep()
    {

        $m = new Text('abc');
        $this->assertEquals(Message::NORMAL, $m->getVerbosityName());
        $this->assertEquals('abc', $m->getTrimText());
        $this->assertEquals(Text::INFO, $m->getStyle());

        $u = unserialize(serialize($m));

        $this->assertEquals(Message::NORMAL, $u->getVerbosityName());
        $this->assertEquals('abc', $u->getTrimText());
        $this->assertEquals(Text::INFO, $u->getStyle());

    }

}