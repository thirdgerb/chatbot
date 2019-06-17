<?php

/**
 * Class RecursiveContainer
 * @package Commune\Container
 */

namespace Commune\Container;


/**
 * 使用方法:
 * 创建一个父类, 父类 use ContainerTrait
 * 然后子类继承父类, 同时 use RecursiveContainer, 就能生效.
 *
 *
 * Trait RecursiveContainer
 * @package Commune\Container
 */
trait RecursiveContainer
{
    use ContainerTrait;

    /**
     * 不用静态属性, 静态属性在子类继承上会有问题.
     *
     * @var ContainerContract
     */
    protected $parentContainer;

    /**
     * RecursiveContainer constructor.
     * @param ContainerContract $parentContainer
     */
    public function __construct(ContainerContract $parentContainer)
    {
        $this->parentContainer = $parentContainer;
    }

    public function getParentContainer() : ContainerContract
    {
        return $this->parentContainer;
    }

    public function has($abstract)
    {
        return $this->bound($abstract) || $this->parentContainer->has($abstract);
    }

    /**
     * @param string $abstract
     * @param array $parameters
     * @return mixed
     * @throws
     */
    public function make(string $abstract, array $parameters = [])
    {
        // 做个最高效的判断环节. 绝大部分都是单例.
        if (isset($this->shared[$abstract])) {
            return $this->shared[$abstract];
        }

        // 优先自己绑定的对象.
        // 只有自己没有绑定, 且父容器有绑定的情况下, 才通过父类来做实例化.
        if (!$this->bound($abstract) && $this->parentContainer->has($abstract)) {
            return $this->parentContainer->make($abstract, $parameters);
        }

        return $this->resolve($abstract, $parameters);
    }


    public function flush(): void
    {
        $this->flushContainer();
        $this->flushInstance();
        $this->parentContainer->flush();
    }

}