<?php

/**
 * Class OptionTest
 * @package Commune\Test\Support
 */

namespace Commune\Test\Support;


use Commune\Support\Struct;
use PHPUnit\Framework\TestCase;

class OptionTest extends TestCase
{
    public function testPredefinedOption()
    {
        $op1 = new Option1();
        $this->assertEquals(1, $op1->a);
        $this->assertEquals(1, $op1->b->a);
        $this->assertEquals(1, $op1->b->b);

        $op1n = new Option1([
            'a' => 2,
            'b' => [
                'a' => 2,
            ]
        ]);

        $this->assertEquals(2, $op1n->a);
        $this->assertEquals(2, $op1n->b->a);
        $this->assertEquals(1, $op1n->b->b);
    }

    public function testOptionToArray()
    {
        $c = new Option3();

        $this->assertEquals(Option3::stub(), $c->toArray());
        $this->assertEquals(Option3::stub(), $c->toRecursiveArray());
    }

    public function testOptionIterator()
    {
        $c = new Option3([
            'option1' => Option1::stub(),
            'option2' => [
                Option2::createById(1)->toArray(),
                Option2::createById(2)->toArray(),
                Option2::createById(3)->toArray(),
            ]
        ]);


        $t = $c->toArray()['option2'] ?? [];
        $this->assertTrue(is_array($t));

        $this->assertEquals(3, count($c->option2));
        foreach ($c->option2 as $i => $o) {
            $this->assertTrue($o instanceof Option2);
            $this->assertEquals($i + 1, $o->getId());

            $this->assertEquals($t[$i], $o->toArray());
        }


    }

    public function testForEach()
    {
        $test = new Option3([
            'option2' => [
                Option2::createById(1)->toArray(),
                Option2::createById(2)->toArray(),
                Option2::createById(3)->toArray(),
            ]
        ]);

        $this->assertEquals(3, count($test->option2));

        $i = 0;
        foreach ($test->option2 as $option2) {
            $i ++;
            $this->assertEquals($i, $option2->getId());
        }

        $this->assertEquals(3, $i);
    }


}


/**
 * Class Option1
 * @package Commune\Test\Support
 *
 * @property-read  int $a
 * @property-read Option2 $b
 */
class Option1 extends Option
{
    protected static $associations =[
        'b' => Option2::class,
    ];

    public static function stub(): array
    {
        return [
            'a' => 1,
            'b' => Option2::stub()
        ];
    }

}

/**
 * Class Option2
 * @package Commune\Test\Support
 *
 * @property-read int $a
 * @property-read int $b
 */
class Option2 extends Option
{
    const IDENTITY = 'a';

    public static function stub(): array
    {
        return [
            'a' => 1,
            'b' => 1,
        ];
    }

}

/**
 * @property-read Option1 $option1
 * @property-read Option2[] $option2
 */
class Option3 extends Option
{
    protected static $associations =[
        'option1' => Option1::class,
        'option2[]' => Option2::class,
    ];

    public static function stub(): array
    {
        return [
            'option1' => Option1::stub(),
            'option2' => [
                Option2::stub(),
            ]
        ];
    }
}