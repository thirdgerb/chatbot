<?php


namespace Commune\Test\Chatbot\App\Messages;


use Commune\Chatbot\App\Messages\QA\Choice;
use Commune\Chatbot\App\Messages\QA\Choose;
use Commune\Chatbot\App\Messages\Text;
use PHPUnit\Framework\TestCase;

class ChooseTest extends TestCase
{

    public function testChoice()
    {
        $c = new Choose('test', [
            'a',
            'b',
        ]);

        $a = $c->parseAnswer(new Text('1'));
        $this->assertTrue($a instanceof Choice);
        $this->assertEquals('b', $a->toResult());
    }

}