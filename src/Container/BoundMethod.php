<?php

namespace Commune\Container;

use ReflectionMethod;
use ReflectionFunction;

/**
 * Class BoundMethod
 *
 * 抄的 laravel container, 做了一些简化.
 * 主要是简化了 "class@method" 这种模式,
 * 也去掉了 laravel 的 method binding
 *
 * @see \Illuminate\Container\BoundMethod
 */
class BoundMethod
{
    /**
     * Call the given Closure / class@method and inject its dependencies.
     *
     * @param  ContainerContract  $container
     * @param  callable|string  $callback
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \ReflectionException
     * @throws \InvalidArgumentException
     */
    public static function call(ContainerContract $container, callable $callback, array $parameters = [])
    {
        if (static::isClassNameWithNonStaticMethod($callback)) {
            $callback[0] = $container->make($callback[0]);
        }

        return call_user_func_array(
            $callback, static::getMethodDependencies($container, $callback, $parameters)
        );
    }

    /**
     * @param callable $caller
     * @return bool
     * @throws \ReflectionException
     */
    public static function isClassNameWithNonStaticMethod(callable $caller) : bool
    {
        if (is_array($caller) && is_string($caller[0])) {
            $r = new ReflectionMethod($caller[0], $caller[1]);
            return ! $r->isStatic();
        }
        return false;
    }

    /**
     * Get all dependencies for a given method.
     *
     * @param  ContainerContract  $container
     * @param  callable|string  $callback
     * @param  array  $parameters
     * @return array
     *
     * @throws \ReflectionException
     */
    protected static function getMethodDependencies($container, $callback, array $parameters = [])
    {
        $dependencies = [];

        foreach (static::getCallReflector($callback)->getParameters() as $parameter) {
            static::addDependencyForCallParameter($container, $parameter, $parameters, $dependencies);
        }

        return array_merge($dependencies, $parameters);
    }

    /**
     * Get the proper reflection instance for the given callback.
     *
     * @param  callable|string $callback
     * @return \ReflectionFunctionAbstract
     *
     * @throws \ReflectionException
     */
    protected static function getCallReflector($callback)
    {
        if (is_string($callback) && strpos($callback, '::') !== false) {
            $callback = explode('::', $callback);
        }

        // __invoke
        // 可以给laravel 上报一个bug了.
        if (is_object($callback)) {
            return new ReflectionMethod($callback, '__invoke');
        }

        return is_array($callback)
                        ? new ReflectionMethod($callback[0], $callback[1])
                        : new ReflectionFunction($callback);
    }

    /**
     * Get the dependency for the given call parameter.
     *
     * @param  ContainerContract  $container
     * @param  \ReflectionParameter  $parameter
     * @param  array  $parameters
     * @param  array  $dependencies
     * @return void
     */
    protected static function addDependencyForCallParameter($container, $parameter,
                                                            array &$parameters, &$dependencies)
    {
        if (array_key_exists($parameter->name, $parameters)) {
            $dependencies[] = $parameters[$parameter->name];

            unset($parameters[$parameter->name]);

        } elseif ($parameter->getClass() && array_key_exists($parameter->getClass()->name, $parameters)) {
            $dependencies[] = $parameters[$parameter->getClass()->name];

            unset($parameters[$parameter->getClass()->name]);

        } elseif ($parameter->getClass()) {
            $dependencies[] = $container->make($parameter->getClass()->name);

        } elseif ($parameter->isDefaultValueAvailable()) {
            $dependencies[] = $parameter->getDefaultValue();
        }
    }

}
