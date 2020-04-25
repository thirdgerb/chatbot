<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Support\Struct;

use Commune\Support\Struct\AStruct;
use Commune\Support\Struct\InvalidStructException;
use PHPUnit\Framework\TestCase;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StructTest extends TestCase
{
    public function testExample()
    {
        $aObj = new A();
        $this->assertEquals(1, $aObj->a);
        $this->assertEquals(2, $aObj->b);

        $this->assertEquals([
            'a'=> 1,
            'b'=> 2,
        ], $aObj->toArray());
    }

    public function testBExample()
    {
        $b = new B();
        $this->assertEquals('a', $b->a);
        $this->assertEquals(1, $b->b->a);
        $this->assertEquals(2, $b->b->b);
        $this->assertEquals(B::stub(), $b->toArray());
    }

    public function testBWithNull()
    {
        $b = new B(['a' => '234','b' =>  null]);
        $this->assertEquals('234', $b->a);
        $this->assertNull($b->b);
    }

    public function testTypeError()
    {
        $e = null;
        try {
            //强类型校验
            $obj = new A(['a' => 1.1 ]);
        } catch (\Exception $e) {
        }
        $this->assertTrue($e instanceof InvalidStructException);
        $b = new B(['a' => 123, 'b' => null, 'c' => 1.1]);
        // 经过了弱类型转换.
        $this->assertTrue('123' === $b->a);
        $this->assertTrue(1 === $b->c);
    }

}

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property string $a
 * @property A|null $b
 * @property int $c
 */
class B extends AStruct
{
    // 弱类型校验
    const STRICT = false;

    public static function stub(): array
    {
        return [
            'a' => 'a',
            'b' => A::stub(),
            'c' => 1,
        ];
    }


    public static function relations(): array
    {
        return ['b' => A::class];
    }


}

/**
 * @property int $a
 * @property int $b
 */
class A extends AStruct
{

    public static function stub(): array
    {
        return [
            'a' => 1,
            'b' => 2
        ];
    }

    public function getId()
    {
        return $this->a;
    }


    public static function relations(): array
    {
        return [];
    }

}