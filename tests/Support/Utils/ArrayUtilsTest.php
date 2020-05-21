<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Support\Utils;

use Commune\Support\Utils\ArrayUtils;
use PHPUnit\Framework\TestCase;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ArrayUtilsTest extends TestCase
{


    public function testExpectTokens()
    {
        $tokens = [ 'a', 'b', 'c',];

        $this->assertTrue(ArrayUtils::expectTokens($tokens, ['a', 'b', 'c']));
        $this->assertTrue(ArrayUtils::expectTokens($tokens, ['a', 'c']));
        $this->assertTrue(ArrayUtils::expectTokens($tokens, ['b', 'd'], false));
        $this->assertTrue(ArrayUtils::expectTokens($tokens, ['a', ['b', 'd'], 'c']));


        $this->assertFalse(ArrayUtils::expectTokens($tokens, ['a', 'c', 'd']));
        $this->assertFalse(ArrayUtils::expectTokens($tokens, ['a', 'b', 'c', 'd']));
        $this->assertFalse(ArrayUtils::expectTokens($tokens, ['a', ['e', 'f', 'g']]));


        $this->assertTrue(ArrayUtils::expectTokens($tokens, ['a', 'b', 'c'], false));

        $this->assertTrue(ArrayUtils::expectTokens($tokens, ['a', 'c'], false));
        $this->assertTrue(ArrayUtils::expectTokens($tokens, ['a', 'd'], false));
        $this->assertTrue(ArrayUtils::expectTokens($tokens, ['d', ['b', 'c']], false));


        $this->assertFalse(ArrayUtils::expectTokens($tokens, [['a', 'f'], ['b', 'g']], false));

        $this->assertFalse(ArrayUtils::expectTokens($tokens, ['d', ['b', 'e']], false));



    }

}