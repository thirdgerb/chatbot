<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Ghost\Support;

use Commune\Ghost\Support\ContextUtils;
use PHPUnit\Framework\TestCase;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ContextTypeUtilsTest extends TestCase
{


    public function testIsValidUcl()
    {
        $cases = [
            'hello.world.yes#stage?{a:1,b:2}',
            'hello.world#stage?{a:1,b:2}',
            'hello.world?{a:1,b:2}',
            'hello.world',
            'hello.world?{}',
            'hello.world#stage',
            'hello.world#stage.abc.efg',
        ];

        foreach($cases as $case) {
            $this->assertTrue(ContextUtils::isValidUcl($case));
        }

        $falseCases = [
            // 大小写
            'Hello.world.yes#stage?{a:1,b:2}',
            // 未闭合
            'hello.world.yes#stage?{a:1,b:2',
            // 双 stage
            'hello.world.yes#stage#stage1?{a:1,b:2}',
            // 位置颠倒
            'Hello.world.yes?{a:1,b:2}#stage',

        ];

        foreach($falseCases as $case) {
            $this->assertFalse(ContextUtils::isValidUcl($case));
        }

    }

}