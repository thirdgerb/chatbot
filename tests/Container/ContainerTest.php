<?php

/**
 * Class ContainerTest
 * @package Container
 */

namespace Commune\Test\Container;

use Commune\Container\ContainerContract;
use Commune\Container\ContainerTrait;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        (new Test())->flush();
    }

    public function testInstance()
    {
        $t1 = new Test();
        $t1->instance('abc', 123);
        $this->assertEquals(123, $t1->make('abc'));


        $t2 = new Test();
        $this->assertEquals(123, $t2->make('abc'));

        $t2->instance('abc', 321);
        $this->assertEquals(321, $t1->make('abc'));

    }

    public function testSingleton()
    {
        $t1 = new Test();
        $t1->singleton(Counter::class, function ($container) {
            $this->assertTrue($container instanceof ContainerContract);
            return new Counter(1);

        });

        $this->assertEquals(1, $t1[Counter::class]->getCount());
        // 由于是单例, 所以count 不会增加.
        $this->assertEquals(1, $t1[Counter::class]->getCount());

        $t2 = new Test();
        // new 了一个新实例
        $this->assertEquals(2, $t2[Counter::class]->getCount());


        // 调用方法递增
        $this->assertEquals(2, $t1[Counter::class]->plusNum());
        $this->assertEquals(3, $t1[Counter::class]->plusNum());
        // t2 应该不会share同一个实例.
        $this->assertEquals(2, $t2[Counter::class]->plusNum());

    }


    public function testDestruct()
    {
        Test::$destroy = 0;
        $test = new Test(1);
        $this->assertEquals(1, $test->des);
        $this->parse($test);
        $this->assertEquals(2, Test::$destroy);
    }

    protected function parse(Test $test)
    {
        $a = $test->newInstance(2);
        $this->assertEquals(2, $a->des);
        // 没有容器实例销毁, 所以不会触发
        $this->assertEquals(0, Test::$destroy);
    }


    public function testMakeByParameters()
    {
        $c = new Test();

        $depend = new class implements Depend { public $a; };
        $depend->a = 123;
        $inject = $c->make(InjectTo::class, [
            Depend::class => $depend,
            'b' => 3,
        ]);

        $this->assertTrue($inject instanceof InjectTo);
        $this->assertEquals(123, $inject->a);
        $this->assertEquals(3, $inject->b);
    }


    public function testCallCallableInstance()
    {
        $c = new Test();
        $c->instance(static::class, $this);

        $invoker = new class {
            public function __invoke(ContainerTest $test)
            {
                return $test;
            }
        };

        $i = $c->call($invoker);
        $this->assertTrue($i instanceof self);

    }
}

class Test implements ContainerContract
{
    use ContainerTrait;

    public static $destroy = 0;

    public $des;

    public function __construct(int $des = 0)
    {
        $this->des = $des;
    }

    public function newInstance(int $newDesc)
    {
        return new static($newDesc);
    }

    public function __destruct()
    {
        self::$destroy = $this->des;
    }
}


/**
 * @property int $a
 */
interface Depend {
}

class InjectTo {

    public $a;

    public $b;

    public function __construct(Depend $depend, int $b)
    {
        $this->a = $depend->a;
        $this->b = $b;
    }
}


class Counter
{
    public static $count = 0;

    public $num;

    public function __construct(int $num)
    {
        $this->num = $num;
        static::$count ++;
    }

    public function getCount() : int
    {
        return static::$count;
    }

    public function plusNum() : int
    {
        $this->num ++;
        return $this->num;
    }


}
