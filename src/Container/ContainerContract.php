<?php

/**
 * Class ContainerInterface
 * @package Container
 */

namespace Commune\Container;

use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\ContainerInterface as Psr;
use Closure;
use ArrayAccess;

/**
 * 模仿 illuminate 的 container,
 * 简化了部分, 主要是为了能够多实例兼容register
 *
 * Interface ContainerInterface
 * @package Container
 *
 * @see \Illuminate\Contracts\Container\Container
 */
interface ContainerContract extends Psr, ArrayAccess
{
    /**
     * Alias a type to a different name.
     *
     * @param  string  $abstract
     * @param  string  $alias
     * @return void
     *
     * @throws \LogicException
     */
    public function alias(string $abstract,string $alias) : void;

    /**
     * Determine if the given abstract type has been bound.
     * 只检查当前容器, 不检查所有父容器.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function bound(string $abstract) : bool;

    /**
     * Register a binding with the container.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     */
    public function bind(string $abstract, $concrete = null, bool $shared = false) : void;

    /**
     * Register a binding if it hasn't already been registered.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     */
    public function bindIf(string $abstract, $concrete = null, bool $shared = false);

    /**
     * Register a shared binding in the container.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function singleton(string $abstract, $concrete = null) : void;


    /**
     * Register an existing instance as shared in the container.
     *
     * @param  string  $abstract
     * @param  mixed   $instance
     * @return mixed
     */
    public function instance(string $abstract, $instance);


    /**
     * share existing instance as shared in the container instance.
     *
     * @param  string  $abstract
     * @param  mixed   $instance
     * @return mixed
     */
    public function share(string $abstract, $instance);

    /**
     * Get a closure to resolve the given type from the container.
     *
     * @param  string  $abstract
     * @return \Closure
     */
    public function factory(string $abstract) : Closure;

    /**
     * Flush the container of all bindings and resolved instances.
     *
     * @return void
     */
    public function flush() : void;

    /**
     * Resolve the given type from the container.
     *
     * @param  string  $abstract
     * @param  array  $parameters
     * @return mixed
     *
     * @throws
     * 实际上是 throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function make(string $abstract, array $parameters = []);

    /**
     * "Extend" an abstract type in the container.
     *
     * @param  string    $abstract
     * @param  \Closure  $closure
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function extend(string $abstract, Closure $closure) : void;

    /**
     * @param callable $caller
     * @param array $parameters
     * @return mixed
     * @throws \ReflectionException
     * @throws BindingResolutionException
     */
    public function call(callable $caller, array $parameters = []);

}