<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Message\Abstracted;

use Commune\Framework\Prototype\Abstracted\IComprehension;
use Commune\Support\Babel\Babel;
use PHPUnit\Framework\TestCase;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IComprehensionTest extends TestCase
{

    public function testToArray()
    {
        $c = new IComprehension();
        $arr = $c->toArray();

        foreach(IComprehension::PROPERTIES as $name => $value) {
            $this->assertTrue(is_a($c->{$name}, $value));
            $this->assertArrayHasKey($name, $arr);
        }
    }

    public function testSerialize()
    {
        $c = new IComprehension();
        $c->intention->setMatchedIntent('hello');

        $s = Babel::getResolver()->serialize($c);
        $c2 = Babel::getResolver()->unSerialize($s);

        $this->assertEquals($c->toArray(), $c2->toArray());

    }

}