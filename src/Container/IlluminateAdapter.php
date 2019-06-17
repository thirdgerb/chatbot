<?php

/**
 * Class IlluminateAdapter
 * @package Commune\Container
 */

namespace Commune\Container;

use Illuminate\Contracts\Container\Container;
use Closure;

/**
 * Class IlluminateAdapter
 * @package Commune\Container
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IlluminateAdapter implements ContainerContract
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * IlluminateAdapter constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function alias(string $abstract, string $alias): void
    {
        $this->container->alias($abstract, $alias);
    }

    public function bound(string $abstract): bool
    {
        return $this->container->bound($abstract);
    }

    public function bind(string $abstract, $concrete = null, bool $shared = false): void
    {
        $this->container->bind($abstract, $concrete, $shared);
    }

    public function bindIf(string $abstract, $concrete = null, bool $shared = false) : void
    {
        $this->container->bindIf($abstract, $concrete, $shared);
    }

    public function singleton(string $abstract, $concrete = null): void
    {
        $this->container->singleton($abstract, $concrete);
    }

    public function instance(string $abstract, $instance)
    {
        $this->container->instance($abstract, $instance);
    }

    public function share(string $abstract, $instance)
    {
        $this->container->instance($abstract, $instance);
    }

    public function factory(string $abstract): Closure
    {
        return $this->container->factory($abstract);
    }

    public function flush(): void
    {
        $this->container->flush();
    }

    public function make(string $abstract, array $parameters = [])
    {
        return $this->container->make($abstract, $parameters);
    }

    public function call(callable $caller, array $parameters = [])
    {
        if (BoundMethod::isClassNameWithNonStaticMethod($caller)) {
            $caller[0] = $this->container->make($caller[0]);
        }
        return $this->container->call($caller, $parameters);
    }


    public function resolved(string $abstract): bool
    {
        return $this->container->resolved($abstract);
    }

    public function get($id)
    {
        return $this->container->get($id);
    }

    public function has($id)
    {
        return $this->container->has($id);
    }

    public function extend(string $abstract, Closure $closure) : void
    {
        $this->container->extend($abstract, $closure);
    }


    /**
     * Determine if a given offset exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->container->bound($key);
    }

    /**
     * Get the value at a given offset.
     *
     * @param  string  $key
     * @return mixed
     * @throws
     */
    public function offsetGet($key)
    {
        return $this->container->make($key);
    }

    /**
     * Set the value at a given offset.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->container->bind($key, $value instanceof Closure ? $value : function () use ($value) {
            return $value;
        });
    }

    /**
     * Unset the value at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        if (method_exists($this->container, 'offsetUnset')) {
            $this->container->offsetUnset($key);
        }
    }


}