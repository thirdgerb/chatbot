<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Support\Parameter;

use Commune\Support\Parameter\IParamDefs;
use PHPUnit\Framework\TestCase;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ParameterTest extends TestCase
{


    public function testParameterModule()
    {
        $className = self::class;
        $defs = IParamDefs::create($arr = [
            'a:string' => '',
            'b:null|string' => null,
            'ccc:string[]|int[]|null' => [],
            "d:$className" => null,
        ]);

        $this->assertEquals($arr, $defs->getDefinitions());


        $this->assertTrue($defs->hasParam('a'));
        $this->assertTrue($defs->hasParam('b'));
        $this->assertTrue($defs->hasParam('ccc'));
        $this->assertTrue($defs->hasParam('d'));

        // test a
        $a = $defs->getParam('a');
        $this->assertTrue($a->isValid('abc'));
        // 123 could be string
        $this->assertTrue($a->isValid(123));
        $this->assertEquals('123', $a->parse(123));
    }

}