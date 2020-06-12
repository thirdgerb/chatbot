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
use Commune\Support\Utils\ArrayUtils;
use PHPUnit\Framework\TestCase;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class HandlerMatcherTest extends TestCase
{

    public function testMatcher()
    {
        $option1 = new ProtocalHandlerOpt([
            'protocal' => IText::class,
            'handler' => 'a',
            'filters' => ['*'],
            'params' => [],
        ]);

        $option2 = new ProtocalHandlerOpt([
            'protocal' => IText::class,
            'handler' => 'b',
            'filters' => ['hello.*.world'],
            'params' => [],
        ]);

        $manager = new ProtocalMatcher([$option2, $option1]);

        $p = new IText('hello.to.world');

        $this->assertEquals(2, ArrayUtils::count($manager->matchEach($p)));

        $first = ArrayUtils::first($manager->matchEach($p));
        $this->assertTrue($first instanceof ProtocalHandlerOpt);
        $this->assertEquals('b', $first->handler);
        $this->assertEquals('b', $manager->matchFirst($p)->handler);


        // 正则不匹配
        $p = new IText('hello.world');
        $this->assertEquals(1, ArrayUtils::count($manager->matchEach($p)));
        $first = ArrayUtils::first($manager->matchEach($p));
        $this->assertTrue($first instanceof ProtocalHandlerOpt);
        $this->assertEquals('a', $first->handler);
        $this->assertEquals('a', $manager->matchFirst($p)->handler);
    }


}