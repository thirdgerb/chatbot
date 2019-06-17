<?php

/**
 * Class DoubleContainerTest
 * @package Commune\Test\Container
 */

namespace Commune\Test\Container;


use Commune\Container\ContainerContract;
use Commune\Container\ContainerTrait;
use PHPUnit\Framework\TestCase;

class DoubleContainerTest extends TestCase
{

    public function testABNotConflict()
    {
        $a = new DoubleContainerA();
        $b = new DoubleContainerB();

        $a->instance('abc', 1);
        $b->instance('abc', 2);

        // 两个instance 应该不会冲突
        $this->assertEquals(1, $a['abc']);
        $this->assertEquals(2, $b['abc']);

        $c = new DoubleContainerC();
        $this->assertEquals(1, $c['abc']);
    }


}

class DoubleContainerA implements ContainerContract
{
    use ContainerTrait;

}

class DoubleContainerB implements ContainerContract
{
    use ContainerTrait;
}

// 应该会导致数据共享.
class DoubleContainerC extends DoubleContainerA {

}