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

//
//    public function testIsValidUcl()
//    {
//        $cases = [
//            'hello.world.yes#stage?a=1&b=2}',
//            'hello.world#stage?a=1&b=2}',
//            'hello.world?a=1&b=2}',
//            'hello.world',
//            'hello.world?',
//            'hello.world#stage',
//            'hello.world#stage_abc_efg',
//        ];
//
//        foreach($cases as $case) {
//            $this->assertTrue(ContextUtils::isValidUcl($case));
//        }
//
//        $falseCases = [
//            // 大小写
//            'Hello.world.yes#stage?{a:1,b:2}',
//            // 未闭合
//            'hello.world.yes#stage?{a:1,b:2',
//            // 双 stage
//            'hello.world.yes#stage#stage1?{a:1,b:2}',
//            // 位置颠倒
//            'Hello.world.yes?{a:1,b:2}#stage',
//
//        ];
//
//        foreach($falseCases as $case) {
//            $this->assertFalse(ContextUtils::isValidUcl($case));
//        }
//
//    }

    public function testValidContextName()
    {

        $this->assertTrue(ContextUtils::isValidContextName('a.b.c'));

        // 字母+数字
        $this->assertTrue(ContextUtils::isValidContextName('abc0.ef1g.hij'));
        // 没有命名空间
        $this->assertTrue(ContextUtils::isValidContextName('abc'));
        // 出现了大写
        $this->assertFalse(ContextUtils::isValidContextName('abc.E'));
        // 数字开头
        $this->assertFalse(ContextUtils::isValidContextName('abc.0ac'));
        // 出现了 -
        $this->assertFalse(ContextUtils::isValidContextName('abc.ak-f'));
    }

    public function testValidStageName()
    {
        $validCases = [
            'bc.efg_hij',
            'ab0c.ef1g.h3ij',
            'f.a0_1_2_3_4',

        ];

        $invalidCases = [
            'Ab0c.tt_ef1g_h3ij',
            'ab0c.tt_Ef1g_h3ij'

        ];

        foreach ($validCases as $case) {
            $this->assertTrue(ContextUtils::isValidStageFullName($case), $case);
        }

        foreach ($invalidCases as $case) {
            $this->assertFalse(ContextUtils::isValidStageFullName($case), $case);
        }

        $this->assertTrue(ContextUtils::isValidStageName('efg_hij'));
        $this->assertTrue(ContextUtils::isValidStageName('tt_ef1g_h3ij'));
        $this->assertFalse(ContextUtils::isValidStageName('tt_Ef1g_h3ij'));

    }

    public function testStageNameCases()
    {
        $this->assertTrue(ContextUtils::isValidStageName('v2_technology_exploration'));
    }

}