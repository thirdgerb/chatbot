<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Ghost;

use Commune\Blueprint\Ghost\Ucl;
use PHPUnit\Framework\TestCase;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class UclTest extends TestCase
{
    protected $validCases = [
        'abc.efg.ijk/stage_name?a=1&b=2',
    ];

    protected $invalidCases = [];

    public function testUcl()
    {
        foreach ($this->validCases as $case) {
            $this->doTestUcl($case);
        }

        foreach ($this->invalidCases as $case) {
            $this->assertFalse(Ucl::isValid($case), $case);
        }
    }

    protected function doTestUcl(string $case)
    {
        $this->assertTrue(Ucl::isValid($case), $case);

        // case1
        $caseObj = Ucl::decodeUcl($case);

        // case2
        $case2 = $caseObj->toEncodedUcl();
        $case2Obj = Ucl::decodeUcl($case2);

        // case3
        $case3 = $case2Obj->toEncodedUcl();
        $case3Obj = Ucl::decodeUcl($case3);

        // ucl字符串相等
        $this->assertEquals($case2, $case3, $case);

        // 字符串校验
        $this->assertTrue($case3Obj->atSameContext($case), $case);

        // at && equals
        $this->assertTrue($caseObj->equals($case2Obj), $case);
        $this->assertTrue($case2Obj->equals($case3Obj), $case);


        $case4Obj = $case3Obj->goStage('test_some_not_exists_stage');

        $this->assertTrue($case4Obj->atSameContext($case3Obj));
        $this->assertTrue($case4Obj->isSameContext($case3Obj));
        $this->assertFalse($case4Obj->equals($case3Obj));


    }

}