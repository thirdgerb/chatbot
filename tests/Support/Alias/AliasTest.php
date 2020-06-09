<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Support\Alias;

use Commune\Support\Alias\AliasesTest;
use PHPUnit\Framework\TestCase;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class AliasTest extends TestCase
{

    public function testAlias()
    {
        $this->assertEquals('t', AliasesTest::getAliasOfOrigin('test'));
        $this->assertEquals('f', AliasesTest::getAliasOfOrigin('foo'));
        $this->assertEquals('b', AliasesTest::getAliasOfOrigin('bar'));

        $this->assertEquals('test', AliasesTest::getOriginFromAlias('t'));
        $this->assertEquals('foo', AliasesTest::getOriginFromAlias('f'));
        $this->assertEquals('bar', AliasesTest::getOriginFromAlias('b'));

        AliasesTest::setAlias('hello', 'world');
        $this->assertEquals('hello', AliasesTest::getOriginFromAlias('world'));
        $this->assertEquals('world', AliasesTest::getAliasOfOrigin('hello'));

        $this->assertEquals('some', AliasesTest::getAliasOfOrigin('some'));
        $this->assertEquals('some', AliasesTest::getOriginFromAlias('some'));

    }

}