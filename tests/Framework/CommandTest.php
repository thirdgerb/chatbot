<?php

/**
 * Class CommandTest
 * @package Commune\Chatbot\Test\Framework
 */

namespace Commune\Chatbot\Test\Framework;


use Commune\Chatbot\Framework\Support\ChatbotUtils;
use Illuminate\Console\Parser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\StringInput;

class CommandTest extends TestCase
{

    /**
     * @dataProvider commandMarkTestProvider
     * @param string $char
     * @param string $str
     * @param int $matched
     */
    public function testCommandMark(string $char, string $str, string $matched = null)
    {

        $exp = ChatbotUtils::getCommandStr($str, $char);
        $this->assertEquals($exp, $matched, $str);
    }

    public function commandMarkTestProvider()
    {
        return [
            ['.', '.hello', 'hello'],
            ['/', '/#hello', '#hello'],
            ['/', '//hello', null],
            ['#', '##hello', null],
            ['#', '#hello', 'hello'],
            ['#', '#hel#lo', 'hel#lo'],
            ['#', '#hel    #lo', 'hel    #lo'],
            ['#', ' #hel#lo', null],
        ];
    }


    public function testSymfonyStringInputWithChinese()
    {
        [$name, $arguments, $options] = Parser::parse('你好 {v1} {v2=a} {--选项1} {--选项2=3}');
        $d = new InputDefinition();
        $d->addArguments($arguments);
        $d->addOptions($options);
        $input = new StringInput("测试 命令 --选项1 --选项2");
        $input->bind($d);
        $this->assertEquals("测试", $input->getFirstArgument());
        $this->assertEquals("测试", $input->getArgument('v1'));
        $this->assertEquals("命令", $input->getArgument('v2'));
        $this->assertTrue($input->hasOption("选项1"));
        $this->assertTrue($input->hasOption("选项2"));
        //$this->assertEquals(3, $input->getOption('选项2'));
    }
}