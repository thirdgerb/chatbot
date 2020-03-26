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

    public function testSuggestions()
    {
        $q = new VbQuestion(
            '请选择',
            [
                1 => 'A1',
                3 => 'A2',
                'A' => 'A3',
                'Abc' => 'A4',
                'bc' => 'A5',
                'bcd' => 'A6',
            ],
            null,
            'fff'
        );

        // 字符串 1
        $a = $q->parseAnswer($this->createSessionMocker('1'));
        $this->assertEquals('A1', $a->toResult());
        $this->assertTrue(1 === $a->getChoice());

        // 字符串 2
        $a = $q->parseAnswer($this->createSessionMocker('2'));
        $this->assertEquals('A2', $a->toResult());
        // php 数组用字符串整数做key, 会被转义成整数
        $this->assertTrue(3 === $a->getChoice());

        // 字符串3
        $a = $q->parseAnswer($this->createSessionMocker('a'));
        // 小写字母 'a' 会匹配大写字母, 虽然多个序号有 A 字母, 完全匹配优先级最高
        $this->assertEquals('A3', $a->toResult());
        // 返回的 choice 值是 suggestions 的序号 A, 而不是输入的小写 a
        $this->assertTrue('A' === $a->getChoice());

        // 字符串4
        $a = $q->parseAnswer($this->createSessionMocker('bc'));
        // 三个选项有 bc, 但只有一个是完全匹配
        $this->assertEquals('A5', $a->toResult());

        // 字符串5
        $a = $q->parseAnswer($this->createSessionMocker('cd'));
        // cd 局部匹配了 'bcd', 由于是唯一匹配, 所以认为命中
        $this->assertEquals('A6', $a->toResult());

        // 字符串6
        $a = $q->parseAnswer($this->createSessionMocker('b'));
        // b 命中了三个选项, 但不是唯一, 所以不认为命中了选项
        $this->assertEquals('b', $a->toResult());
        $this->assertNull($a->getChoice());


        // 用 '.' 表示空输入, 结果命中了默认值
        $a = $q->parseAnswer($this->createSessionMocker('.'));
        $this->assertEquals('fff', $a->toResult());
        $this->assertNull($a->getChoice());

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