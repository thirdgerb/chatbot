<?php


namespace Commune\Test\Chatbot\Framework\Conversation;


use Commune\Chatbot\Framework\Conversation\IncomingMessageImpl;
use Illuminate\Support\Collection;
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

        $entities = new Collection([]);


        $i->addPossibleIntent('a', $entities, 1);
        $i->addPossibleIntent('b',$entities , 2);
        $i->addPossibleIntent('c',$entities , 3);
        $i->addPossibleIntent('d',$entities , 4);

        $i->setHighlyPossibleIntentNames(['a', 'b', 'c']);

        $this->assertEquals('c', $i->getMostPossibleIntent());
    }

}