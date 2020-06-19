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

use Commune\Framework\Log\IConsoleLogger;
use Commune\Message\Host\Convo\IText;
use Commune\Message\Host\IIntentMsg;
use Commune\Support\Protocal\HandlerOption;
use Commune\Support\Protocal\ProtocalMatcher;
use Commune\Support\Protocal\ProtocalOption;
use Commune\Support\Utils\ArrayUtils;
use PHPUnit\Framework\TestCase;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ProtocalMatcherTest extends TestCase
{

    public function testMatcher()
    {
        $option1 = new ProtocalOption([
            'protocal' => IText::class,
            'handlers' => [
                [
                    'handler' => 'a',
                    'filters' => [],
                    'params' => [],
                ],
            ],
        ]);

        $option2 = new ProtocalOption([
            'protocal' => IText::class,
            'interface' => TestCase::class,
            'handlers' => [
                [
                    'handler' => static::class,
                    'filters' => ['hello.*.world'],
                    'params' => [],
                ]
            ],
        ]);

        $logger = new IConsoleLogger();

        $manager = new ProtocalMatcher($logger, [$option1, $option2]);

        // 没有设置任何特殊规则, 所以两个都会匹配到.
        $p = IText::instance('hello.to.world');
        $gen = $manager->matchEach($p);
        $this->assertEquals(2, ArrayUtils::countIterable($gen));

        // 预期匹配到第一个.
        $first = ArrayUtils::first($manager->matchEach($p));
        $this->assertTrue($first instanceof HandlerOption);
        $this->assertEquals('a', $first->handler);
        $this->assertEquals('a', $manager->matchFirst($p)->handler);


        // 预期加入 interface 后, 只能匹配到第二个
        $this->assertEquals(static::class, $manager->matchFirst($p, TestCase::class)->handler);

        // 正则不匹配
        $p = IText::instance('hello.world');
        $this->assertEquals(1, ArrayUtils::countIterable($manager->matchEach($p)));

        $first = ArrayUtils::first($manager->matchEach($p));
        $this->assertTrue($first instanceof HandlerOption);
        $this->assertEquals('a', $first->handler);
        $this->assertEquals('a', $manager->matchFirst($p)->handler);

        // 协议不匹配.
        $p = new IIntentMsg('hello.to.world');
        $this->assertEquals(0, ArrayUtils::countIterable($manager->matchEach($p)));

    }


}