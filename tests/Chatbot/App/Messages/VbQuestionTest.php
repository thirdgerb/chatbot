<?php


namespace Commune\Test\Chatbot\App\Messages;


use Commune\Chatbot\App\Messages\QA\VbQuestion;
use Commune\Chatbot\App\Messages\Text;
use PHPUnit\Framework\TestCase;

class VbQuestionTest extends TestCase
{

    public function testVbQuestion()
    {
        $q = new VbQuestion('请输入任何文字', []);
        $a = $q->parseAnswer(new Text("任何文字"));
        $this->assertEquals('任何文字', $a->toResult());

    }

}