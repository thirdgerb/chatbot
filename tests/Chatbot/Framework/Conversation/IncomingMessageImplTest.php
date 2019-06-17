<?php


namespace Commune\Test\Chatbot\Framework\Conversation;


use Commune\Chatbot\Framework\Conversation\IncomingMessageImpl;
use PHPUnit\Framework\TestCase;

class IncomingMessageImplTest extends TestCase
{

    public function testHighlyPossibleFunc()
    {
        /**
         * @var IncomingMessageImpl $i
         */
        $i = $this->getMockBuilder(IncomingMessageImpl::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();


        $i->addPossibleIntent('a', [], 1);
        $i->addPossibleIntent('b', [], 2);
        $i->addPossibleIntent('c', [], 2);
        $i->addPossibleIntent('d', [], 3);

        $this->assertEquals('d', $i->getHighlyPossibleIntent());
    }

}