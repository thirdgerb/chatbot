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

}