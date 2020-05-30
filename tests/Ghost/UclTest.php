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
        // full
        'abc.efg.ijk/stage_name?a=1&b=2' => 'abc.efg.ijk.stage_name',

        // no query
        'abc/stage_name' => 'abc.stage_name',

        // only query
        'abc?a=1&b=2' => 'abc',

        // list query
        'abc?a=1&b[]=1&b[]=2' => 'abc',
        'abc?a[1]=1&a[k]=10' => 'abc',
    ];


    protected $invalidCases = [
        // no context
        'stage_name',

        // upper case
        'abc/stageName',
    ];

    public function testUcl()
    {
        foreach ($this->validCases as $case => $intent) {
            $this->doTestUcl($case, $intent);
        }

        foreach ($this->invalidCases as $case) {
            $caseObj = Ucl::decodeUclStr($case);
            $this->assertFalse($caseObj->isValidPattern(), $case);
        }
    }

    protected function doTestUcl(string $case, string $fullname)
    {

        // case1
        $caseObj = Ucl::decodeUclStr($case);
        $this->assertTrue($caseObj->isValidPattern(), $case);
        $this->assertEquals($fullname, $caseObj->getStageFullname());

        // case2
        $case2 = $caseObj->toEncodedStr();
        $case2Obj = Ucl::decodeUclStr($case2);

        // case3
        $case3 = $case2Obj->toEncodedStr();
        $case3Obj = Ucl::decodeUclStr($case3);

        // ucl字符串相等
        $this->assertEquals($case2, $case3, $case);

        // 字符串校验
        $this->assertTrue($case3Obj->atSameContext($caseObj), $case);

        // at && equals
        $this->assertTrue($caseObj->equals($case2Obj), $case);
        $this->assertTrue($case2Obj->equals($case3Obj), $case);


        $case4Obj = $case3Obj->goStage('test_some_not_exists_stage');

        $this->assertTrue($case4Obj->atSameContext($case3Obj));
        $this->assertTrue($case4Obj->isSameContext($case3Obj));
        $this->assertFalse($case4Obj->equals($case3Obj));


    }

}