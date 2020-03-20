<?php

namespace Commune\Chatbot\Framework\Utils;

use Psr\Container\ContainerInterface;

/**
 * @see https://github.com/illuminate/pipeline
 *
 * 将laravel 的闭包洋葱式管道接过来, 当成同步管道来使用.
 *
 * Class OnionPipeline
 * @package Commune\Chatbot\Framework\Pipelines
 */
class OnionPipeline
{
    /**
     * The container implementation.
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * The array of class pipes.
     *
     * @var array
     */
    protected $pipes = [];

    /**
     * The method to call on each pipe.
     *
     * @var string
     */
    protected $method = 'handle';

    /**
     * OnionPipeline constructor.
     * @param ContainerInterface $container
     * @param array $pipes
     */
    public function __construct(ContainerInterface $container, array $pipes = [])
    {
        $this->container = $container;
        $this->pipes = $pipes;
    }

    public function buildPipeline(\Closure $destination) : \Closure
    {
        return array_reduce(
            array_reverse($this->pipes),
            $this->carry(),
            $this->prepareDestination($destination)
        );

    }

    public function send($passable, \Closure $destination)
    {
        $pipeline =  $this->buildPipeline($destination);
        return $pipeline($passable);
    }

    /**
     * 通过一个字符串标明的管道.
     * @param  string|callable  $pipe
     * @return $this
     */
    public function through($pipe) : self
    {
        $this->pipes[] = $pipe;
        return $this;
    }

    /**
     * get the final piece of the closure onion
     *
     * @param callable $destination
     * @return \Closure
     */
    protected function prepareDestination(callable $destination)
    {
        return function ($passable) use ($destination) {
            return $destination($passable);
        };
    }


    /**
     * Set the method to call on the pipes.
     *
     * @param  string  $method
     * @return $this
     */
    public function via($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Parse full pipe string to get name and parameters.
     *
     * @param  string $pipe
     * @return array
     */
    protected function parsePipeString(string $pipe) : array
    {
        [$name, $parameters] = $this->explodePipeString($pipe);

        if (is_string($parameters)) {
            $parameters = explode(',', $parameters);
        }

        return [$name, $parameters];
    }

    /**
     * @param string $pipe
     * @return array
     */
    protected function explodePipeString(string $pipe) : array
    {
        return array_pad(explode(':', $pipe, 2), 2, []);
    }

    /**
     * Get a Closure that represents a slice of the application onion.
     *
     * @return \Closure
     */
    protected function carry()
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {

                if (is_callable($pipe)) {
                    // If the pipe is an instance of a Closure, we will just call it directly but
                    // otherwise we'll resolve the pipes out of the container and call it with
                    // the appropriate method and arguments, returning the results back out.
                    $result = $pipe($passable, $stack);
                } else {
                    if (! is_object($pipe)) {
                        [$name, $parameters] = $this->parsePipeString($pipe);

                        // If the pipe is a string we will parse the string and resolve the class out
                        // of the dependency injection container. We can then build a callable and
                        // execute the pipe function giving in the parameters that are required.
                        $pipe = $this->getContainer()->get($name);

                        $parameters = array_merge([$passable, $stack], $parameters);
                    } else {
                        // If the pipe is already an object we'll just make a callable and pass it to
                        // the pipe as-is. There is no need to do any extra parsing and formatting
                        // since the object we're given was already a fully instantiated object.
                        $parameters = [$passable, $stack];

                    }

                    $result = method_exists($pipe, $this->method)
                        ? $pipe->{$this->method}(...$parameters)
                        : $pipe(...$parameters);

                }

                return $result;
            };
        };
    }

}