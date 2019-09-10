<?php


namespace Commune\Test\Chatbot\App\Messages;


use Commune\Chatbot\App\Messages\QA\VbQuestion;
use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Commune\Chatbot\OOHost\Session\Session;
use PHPUnit\Framework\TestCase;

class VbQuestionTest extends TestCase
{

    public function testVbQuestion()
    {
        $session = \Mockery::mock(Session::class);

        /**
         * @var \stdClass $incoming
         */
        $incoming = \Mockery::mock(IncomingMessage::class);


        /**
         * @var \stdClass $session
         */
        $session->incomingMessage = $incoming;
        $incoming->message = new Text("任何文字");

        $q = new VbQuestion('请输入任何文字', []);
        $a = $q->parseAnswer($session);
        $this->assertEquals('任何文字', $a->toResult());

    }

}