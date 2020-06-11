<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Framework\Trans;

use Commune\Framework\Trans\SymfonyTranslatorAdapter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Formatter\MessageFormatter;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SymfonyTranslatorTest extends TestCase
{

    public function testMustacheTrans()
    {
        $formatter = new MessageFormatter();
        $t = SymfonyTranslatorAdapter::mustacheTrans(
            $formatter,
            'hello, {world}! {abc.efg}',
            'zh',

            ['world' => 'world', 'abc.efg' => 'yes']
        );

        $this->assertEquals('hello, world! yes', $t);
    }

}