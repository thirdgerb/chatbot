<?php


namespace Commune\Test\Chatbot\App\Messages;


use Commune\Chatbot\App\Messages\QA\Choice;
use Commune\Chatbot\App\Messages\QA\Choose;
use Commune\Chatbot\App\Mock\MockSession;
use PHPUnit\Framework\TestCase;

class ChooseTest extends TestCase
{
    use MockSession;

    public function testChoice()
    {
        $c = new Choose('test', [
            'a',
            'b',
        ]);

        $session = $this->createSessionMocker('1');

        $a = $c->parseAnswer($session);
        $this->assertTrue($a instanceof Choice);
        $this->assertEquals('b', $a->toResult());
    }

    public function testPartMatch()
    {

        $c = new Choose('test', $choices = [
            'abcdefghi',
            'fghijklmn',
        ]);

        $session = $this->createSessionMocker('bcde');
        $this->fakeSession->expects('getPossibleIntent')->andReturn(null);

        $a = $c->parseAnswer($session);
        $this->assertTrue($a instanceof Choice);
        $this->assertEquals(0, $a->getChoice());
        $this->assertEquals($choices[0], $a->toResult());

        // case 2
        $session = $this->createSessionMocker('jklmn');
        $this->fakeSession->expects('getPossibleIntent')->andReturn(null);

        $a = $c->parseAnswer($session);
        $this->assertTrue($a instanceof Choice);
        $this->assertEquals(1, $a->getChoice());
        $this->assertEquals($choices[1], $a->toResult());

        // case 3 same part
        $session = $this->createSessionMocker('ghi');
        $this->fakeSession->expects('getPossibleIntent')->andReturn(null);

        $a = $c->parseAnswer($session);
        $this->assertNull($a);

    }

    public function testPartIndexMatch()
    {
        $c = new Choose('test', $choices = [
            'abcdefghi' => 1,
            'fghijklmn' => 2,
        ]);

        $keys = array_keys($choices);

        $session = $this->createSessionMocker('bcde'); // 没有'a', 不必从a开始.
        $a = $c->parseAnswer($session);
        $this->assertTrue($a instanceof Choice);
        $this->assertEquals($keys[0], $a->getChoice());
        $this->assertEquals($choices[$keys[0]], $a->toResult());

        // case 2
        $session = $this->createSessionMocker('jklmn'); // 没有'a', 不必从a开始.
        $a = $c->parseAnswer($session);
        $this->assertTrue($a instanceof Choice);
        $this->assertEquals($keys[1], $a->getChoice());
        $this->assertEquals($choices[$keys[1]], $a->toResult());

        // case 3 same part
        $session = $this->createSessionMocker('ghi'); // 没有'a', 不必从a开始.
        $a = $c->parseAnswer($session);
        $this->assertNull($a);

    }


    public function testChooseCase()
    {
        $choose = new Choose('test', [
            '功能点测试',
            '欢迎用户',
            '测试小游戏',
            '开发工具',
        ]);

        $session  =$this->createSessionMocker('测试小游戏');

        $a = $choose->parseAnswer($session);

        $this->assertTrue($a instanceof Choice);
    }
}

