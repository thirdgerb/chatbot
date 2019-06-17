<?php

/**
 * Class ReaderTest
 * @package Commune\Support
 */

namespace Commune\Test\Support\Property;

use Commune\Support\Property\PropertyReader;
use PHPUnit\Framework\TestCase;

class PropertyReaderTest extends TestCase
{
    public function testArray()
    {
        $data = [
            'a' => 1,
            'b' => '2',
        ];

        // 通过getter方法定义过parser 时
        $a = new A('array', $data);
        $this->assertTrue(1 === $a->a);
        $this->assertTrue('2' === $a->b);
        $this->assertNull( $a->c );

        // 没有定义过数组的parser 时
        $b = new B('??', $data);
        $this->assertNull($b->c);
        $this->assertNull($b->d);

        // 用了默认的数组parser
        $this->assertTrue(1 === $b->a);
        $this->assertTrue('2' === $b->b);

        // 通过register 定义过parser 时
        B::register('array', function(array $data, string $name) {
            switch ($name) {
                case 'c' :
                    $name = 'a';
                    break;
                case 'd' :
                    $name = 'b';
                    break;
            }
            return A::getter($data, $name);
        });
        $c = new B('array', $data);
        $this->assertTrue(1 === $c->c);
        $this->assertTrue('2' === $c->d);
        $this->assertNull( $c->e );

        // 没有注册, 用了默认的对象parser

        $d = new B('???', (object)$data);
        $this->assertFalse($d->hasParser());
        $this->assertTrue(1 === $d->a);
        $this->assertTrue('2' === $d->b);
        $this->assertNull( $d->e );
    }

    public function testR()
    {
        $r = new R();
        // 注册过parser 的情况.
        B::register(R::class, function (R $r, string $name) {
            switch ($name) {
                case 'c' :
                    return $r->a;
                case 'd' :
                    return $r->b;
            }
            return null;
        });
        $b = new B(R::class, $r);
        $this->assertTrue(1 === $b->c);
        $this->assertTrue('2' === $b->d);
        $this->assertNull( $b->e );

        // 测试序列化的影响
        $c = unserialize($k = serialize($b));
        $this->assertTrue(1 === $c->c);
        $this->assertTrue('2' === $c->d);
        $this->assertNull( $c->e );

        // 测试注册了父类的parser, 子类实例同样可用.
        $d = new B(R::class, new R1());
        $this->assertTrue(1 === $d->c);
        $this->assertTrue('2' === $d->d);
        $this->assertNull( $d->e );

        // 没有注册过时, 默认取值还是可用.
        $e = new B(R1::class, new R1());
        $this->assertFalse($e->hasParser());
        $this->assertTrue(1 === $e->a);
        $this->assertTrue('2' === $e->b);
        $this->assertNull( $e->e );


    }

    public function testGetters()
    {
        $this->assertEquals(['a', 'b', 'reader'], A::keys());
        $a = new A('array', ['a' =>1, 'b' => '2']);

        $this->assertEquals([
            'a' => 1,
            'b' => '2',
            'reader' => null
        ], $a->toArray());
    }
}


/**
 * Class A
 * @package Commune\Support
 *
 * @property-read null|int $a
 * @property-read null|string b;
 * @property-read \Commune\Support\Property\PropertyReader reader
 */
class A extends PropertyReader
{

    public function arrayGetter(array $data, string $name)
    {
        return static::getter($data, $name);
    }

    public static function getter(array $data, string $name)
    {
        return $data[$name] ?? null;
    }

}

/**
 * Class B
 * @package Commune\Support
 *
 * @property-read int c
 * @property-read string $d
 */
class B extends PropertyReader
{
}

class R
{
    public $a = 1;

    public $b = "2";
}

class R1 extends R {
}
