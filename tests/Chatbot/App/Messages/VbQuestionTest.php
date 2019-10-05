<?php


namespace Commune\Test\Chatbot\App\Messages;


use Commune\Chatbot\App\Messages\QA\Confirm;
use Commune\Chatbot\App\Messages\QA\Confirmation;
use Commune\Chatbot\App\Messages\QA\VbQuestion;
use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Commune\Chatbot\OOHost\Session\Session;
use PHPUnit\Framework\TestCase;

class VbQuestionTest extends TestCase
{
    protected function prepareSession(string $input) : Session
    {
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
        $incoming->message = new Text($input);
        return $session;
    }

    public function testVbQuestion()
    {
        $q = new VbQuestion('请输入任何文字', []);
        $a = $q->parseAnswer($this->prepareSession('任何文字'));
        $this->assertEquals('任何文字', $a->toResult());

    }

    public function testAliases()
    {
        $q = new VbQuestion('要不要[开始](y)', ['y', 'n']);
        $a = $q->parseAnswer($this->prepareSession('开始'));
        $this->assertEquals('y', $a->toResult());
    }

    public function testConfirm()
    {
        $q = new Confirm('要不要[开始](y)', false, 'y', 'n');
        $a = $q->parseAnswer($this->prepareSession('开始'));
        $this->assertTrue($a instanceof Confirmation);
        $this->assertTrue($a->toResult());
    }

}