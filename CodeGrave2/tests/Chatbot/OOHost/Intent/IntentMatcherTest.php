<?php


namespace Commune\Test\Chatbot\OOHost\Intent;


use Commune\Chatbot\OOHost\Context\Intent\IntentMatcher;
use PHPUnit\Framework\TestCase;

class IntentMatcherTest extends TestCase
{

    public function testKeywords()
    {
        $this->assertTrue(
            IntentMatcher::matchWords(
                '测试有没有关键字',
                ['测试', '关键字']
            )
        );

        $this->assertFalse(
            IntentMatcher::matchWords(
                '测有没有键字',
                ['测试', '关键字']
            )
        );

        $this->assertTrue(
            IntentMatcher::matchWords(
                '测试有没有关键字',
                ['测试', ['关键字', 'keyword']]
            )
        );


        $this->assertTrue(
            IntentMatcher::matchWords(
                '测试有没有keyword',
                ['测试', ['关键字', 'keyword']]
            )
        );


        $this->assertTrue(
            IntentMatcher::matchWords(
                '测试有没有keyword啊',
                ['测试', ['关键字', 'keyword'], '没有']
            )
        );
    }

    public function testRegex()
    {
        $this->assertEquals(
            ['a' => 13],
            IntentMatcher::matchRegex(
                '第13个',
                '/第(\d+)个/',
                ['a']
            )
        );

        $this->assertEquals(
            ['a' => '一'],
            IntentMatcher::matchRegex(
                '第一个',
                '/第(零|一)个/',
                ['a']
            )
        );


        $this->assertEquals(
            ['a' => 'a'],
            IntentMatcher::matchRegex(
                '第a个',
                '/第([abc]{1})个/',
                ['a']
            )
        );


        $this->assertEquals(
            ['a' => '一'],
            IntentMatcher::matchRegex(
                '第一个',
                '/第([一二三四五六七]+)个/',
                ['a']
            )
        );


        $this->assertEquals(
            ['a' => 'f'],
            IntentMatcher::matchRegex(
                '第f个',
                '/第(\w+)个/',
                ['a']
            )
        );

        $this->assertEquals(
            ['a' => 'f'],
            IntentMatcher::matchRegex(
                '第f个',
                '/第(.+)个/',
                ['a']
            )
        );
    }

}