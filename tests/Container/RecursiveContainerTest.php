<?php

/**
 * Class RecursiveContainerTest
 * @package Commune\Test\Container
 */

namespace Commune\Test\Container;


use Commune\Container\ContainerContract;
use Commune\Container\ContainerTrait;
use Commune\Container\IlluminateAdapter;
use Commune\Container\RecursiveContainer;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class RecursiveContainerTest extends TestCase
{

    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::setUp();

    }

    public function testInherit()
    {

        $a = new IlluminateAdapter(new Container());
        $a->instance('abc', 1);

        $b = new RecursiveContainerSelf($a);
        $this->assertEquals(1, $b['abc']);

        $c = new RecursiveContainerSelf($a);
        $this->assertEquals(1, $c['abc']);

        $b->instance('abc', 2);
        //新的已经改变
        $this->assertEquals(2, $b['abc']);
        $this->assertEquals(2, $c['abc']);
        //原来的没有改变
        $this->assertEquals(1, $a['abc']);

        //用shared 只会改变自己, 不影响容器静态变量, 也不影响父容器.
        $b->share('abc', 3);

        $this->assertEquals(3, $b['abc']); //读取shared
        $this->assertEquals(2, $c['abc']); //读取静态变量
        $this->assertEquals(1, $a['abc']); //读取父容器
    }

    public function testMakeUnboundConcreteNotByFather()
    {
        $a = new IlluminateAdapter(new Container());
        $b = new RecursiveContainerSelf($a);

        $b->singleton(Foo::class, Bar::class);

        $this->assertTrue($b->make(Foo::class) instanceof Bar);

        $this->assertFalse($a->bound(Foo::class));
    }

}

class RecursiveContainerPrev implements ContainerContract
{
    use ContainerTrait;

}


class RecursiveContainerSelf implements ContainerContract
{
    use RecursiveContainer;

}

class Foo
{

}

class Bar extends Foo
{

}

