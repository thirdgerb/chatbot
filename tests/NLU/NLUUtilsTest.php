<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\NLU;

use Commune\NLU\Support\NLUUtils;
use PHPUnit\Framework\TestCase;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class NLUUtilsTest extends TestCase
{

    public function testIsNotNatureLanguage()
    {
        $this->assertTrue(NLUUtils::isNotNatureLanguage('.'));
        $this->assertTrue(NLUUtils::isNotNatureLanguage('123.45'));

        $this->assertTrue(NLUUtils::isNotNatureLanguage('A'));
        $this->assertTrue(NLUUtils::isNotNatureLanguage('~'));
        $this->assertTrue(NLUUtils::isNotNatureLanguage('#'));



        $this->assertFalse(NLUUtils::isNotNatureLanguage('好'));
    }

}