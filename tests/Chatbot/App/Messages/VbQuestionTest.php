<?php


namespace Commune\Test\Chatbot\App\Messages;


use Commune\Chatbot\App\Messages\QA\Confirm;
use Commune\Chatbot\App\Messages\QA\Confirmation;
use Commune\Chatbot\App\Messages\QA\VbQuestion;
use Commune\Chatbot\App\Mock\MockSession;
use PHPUnit\Framework\TestCase;

class VbQuestionTest extends TestCase
{
    use MockSession;

    public function testVbQuestion()
    {
        $q = new VbQuestion('请输入任何文字', []);
        $a = $q->parseAnswer($this->createSessionMocker('任何文字'));
        $this->assertEquals('任何文字', $a->toResult());

    }

    public function testAliases()
    {
        $q = new VbQuestion('要不要[开始](y)', ['y', 'n']);
        $a = $q->parseAnswer($this->createSessionMocker('开始'));
        $this->assertEquals('y', $a->toResult());

        $a = $q->parseAnswer($this->createSessionMocker('开'));
        $this->assertEquals('y', $a->toResult());
    }

    public function testConfirm()
    {
        $q = new Confirm('要不要[开始](y)', false, 'y', 'n');
        $a = $q->parseAnswer($this->createSessionMocker('开始'));
        $this->assertTrue($a instanceof Confirmation);
        $this->assertTrue($a->toResult());
    }

}