<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Support\Protocal;

use Commune\Message\Host\Convo\IText;
use Commune\Support\Protocal\ProtocalMatcher;
use Commune\Support\Protocal\ProtocalHandlerOpt;
use PHPUnit\Framework\TestCase;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class HandlerMatcherTest extends TestCase
{

    public function testMatcher()
    {
        $option1 = new ProtocalHandlerOpt([
            'group' => 'test1',
            'protocal' => IText::class,
            'handler' => 'a',
            'filter' => ['*'],
            'params' => [],
        ]);

        $option2 = new ProtocalHandlerOpt([
            'group' => 'test2',
            'protocal' => IText::class,
            'handler' => 'b',
            'filter' => ['hello.*.world'],
            'params' => [],
        ]);

        $manager = new ProtocalMatcher([$option1, $option2]);

        $p = new IText('hello.to.world');

        $this->assertEquals('b', $manager->matchHandler('test2', $p));
        $this->assertEquals('a', $manager->matchHandler('test1', $p));

        $p = new IText('hello.world');
        // '*' 匹配
        $this->assertEquals('a', $manager->matchHandler('test1', $p));
        // 正则不匹配
        $this->assertNull($manager->matchHandler('test2', $p));
    }


}