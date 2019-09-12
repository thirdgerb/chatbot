<?php


namespace Commune\Test\Chatbot\App\Messages;


use Commune\Chatbot\App\Messages\QA\Choice;
use Commune\Chatbot\App\Messages\QA\Choose;
use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Commune\Chatbot\OOHost\Session\Session;
use PHPUnit\Framework\TestCase;

class ChooseTest extends TestCase
{

    /**
     */
    public function testChoice()
    {
        $c = new Choose('test', [
            'a',
            'b',
        ]);

        $session = \Mockery::mock(Session::class);
        $session->expects('getPossibleIntent')->andReturn(null);

        /**
         * @var \stdClass $incoming
         */
        $incoming = \Mockery::mock(IncomingMessage::class);


        /**
         * @var \stdClass $session
         */
        $session->incomingMessage = $incoming;
        $incoming->message = new Text('1');


        $a = $c->parseAnswer($session);
        $this->assertTrue($a instanceof Choice);
        $this->assertEquals('b', $a->toResult());
    }

}

