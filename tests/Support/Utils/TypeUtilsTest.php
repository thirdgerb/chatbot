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

use Commune\Support\Utils\TypeUtils;
use PHPUnit\Framework\TestCase;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TypeUtilsTest extends TestCase
{

    public function testValidContextName()
    {
        // 字母+数字
        $this->assertTrue(TypeUtils::isValidContextName('abc0.ef1g.hij'));
        // 没有命名空间
        $this->assertTrue(TypeUtils::isValidContextName('abc'));
        // 出现了大写
        $this->assertFalse(TypeUtils::isValidContextName('abc.E'));
        // 数字开头
        $this->assertFalse(TypeUtils::isValidContextName('abc.0ac'));
        // 出现了 -
        $this->assertFalse(TypeUtils::isValidContextName('abc.ak-f'));
    }

    public function testValidStageName()
    {
        $this->assertTrue(TypeUtils::isValidStageFullName('bc.efg_hij'));
        // 混合数字
        $this->assertTrue(TypeUtils::isValidStageFullName('ab0c.ef1g.h3ij'));
        // 单字母类名
        $this->assertTrue(TypeUtils::isValidStageFullName('f.a0_1_2_3_4'));
        // contextName 有大写
        $this->assertFalse(TypeUtils::isValidStageFullName('Ab0c.tt_ef1g_h3ij'));
        // stageName 有大写
        $this->assertFalse(TypeUtils::isValidStageFullName('ab0c.tt_Ef1g_h3ij'));
        // 下划线开头, 不允许.
        $this->assertFalse(TypeUtils::isValidStageFullName('c._abc'));

    }

}