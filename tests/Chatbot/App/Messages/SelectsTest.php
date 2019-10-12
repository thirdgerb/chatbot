<?php


namespace Commune\Test\Chatbot\App\Messages;


use Commune\Chatbot\App\Messages\QA\Selects;
use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\App\Mock\MockSession;
use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Commune\Chatbot\OOHost\Session\Session;
use PHPUnit\Framework\TestCase;
use Mockery;

class SelectsTest extends TestCase
{
    use MockSession;

    public function testSelects()
    {
        $session = $this->createSessionMocker('a, b, c, d');

        $question = new Selects('test', ['a', 'b' , 'c', 'e', 'f', 'g'], [0, 4, 5]);

        $answer = $question->parseAnswer($session);

        $this->assertEquals('a,b,c', $answer->toResult());
        $this->assertEquals(['a', 'b', 'c'], $answer->getResults());
        $this->assertEquals([0,1,2], $answer->getChoices());
        $this->assertTrue($answer->hasChoice(0));
        $this->assertTrue($answer->hasChoice(1));
        $this->assertTrue($answer->hasChoice(2));

        $session = $this->createSessionMocker('abc');

        $answer2 = $question->parseAnswer($session);
        $this->assertEquals(['a', 'f', 'g'], $answer2->getResults());
        $this->assertEquals([0, 4, 5], $answer2->getChoices());
        $this->assertEquals('a,f,g', $answer2->toResult());
    }

}