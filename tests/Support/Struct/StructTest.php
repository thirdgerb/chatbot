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
use PHPUnit\Framework\TestCase;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StructTest extends TestCase
{
    public function testExample()
    {
        $a = new A();
        $this->assertEquals(1, $a->a);
        $this->assertEquals(2, $a->b);

        $this->assertEquals([
            'a'=> 1,
            'b'=> 2,
        ], $a->toArray());
    }

    public function testBExample()
    {
        $b = new B();

        $this->assertEquals('a', $b->a);
        $this->assertEquals(1, $b->b->a);
        $this->assertEquals(2, $b->b->b);
        $this->assertEquals(B::stub(), $b->toArray());
    }

}

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property string $a
 * @property A $b
 */
class B extends AStruct
{
    public static function stub(): array
    {
        return [
            'a' => 'a',
            'b' => A::stub(),
        ];
    }

    public static function validate(array $data): ? string
    {
        return null;
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

    public static function validate(array $data): ? string
    {
        return null;
    }

    public static function relations(): array
    {
        return [];
    }

}