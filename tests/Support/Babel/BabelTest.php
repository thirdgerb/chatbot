<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Support\Babel;

use Commune\Message\Blueprint\TextMsg;
use Commune\Message\Prototype\IText;
use Commune\Support\Babel\Babel;
use PHPUnit\Framework\TestCase;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class BabelTest extends TestCase
{

    protected function tearDown()
    {
        Babel::setResolver(null);
    }

    public function testDefault()
    {
        $text = new IText("hello\n");
        $input = Babel::getResolver()->serialize($text);
        $text2 = Babel::getResolver()->unSerialize($input);

        $this->assertEquals($text->getTrimmedText(), $text2->getTrimmedText());
        $this->assertEquals($text->getLevel(), $text2->getLevel());
        $this->assertEquals($text->getCreatedAt(), $text2->getCreatedAt());

        $this->assertEquals('hello', $text2->getTrimmedText());
        $this->assertEquals(TextMsg::INFO, $text2->getLevel());
    }

}