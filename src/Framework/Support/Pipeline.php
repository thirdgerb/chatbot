<?php

/**
 * Class Pipeline
 * @package Commune\Chatbot\Framework\Utils
 */

namespace Commune\Chatbot\Framework\Support;


use Commune\Chatbot\Contracts\ChatbotApp;
use Closure;

/**
 * Class Pipeline
 * Copy from laravel
 *
 * @package Commune\Chatbot\Framework\Utils
 */
class Pipeline
{
    /**
     * @var array
     */
    protected $pipes = [];

    /**
     * @var ChatbotApp
     */
    protected $ioc;


    /**
     * The method to call on each pipe.
     *
     * @var string
     */
    protected $method = 'handle';

    protected $pipeline;


    public function __construct(ChatbotApp $app, array $pipes, \Closure $destination = null)
    {
        $this->ioc = $app;
        $this->pipes = $pipes;
        if ($destination) {
            $this->setUpPipe($destination);
        }
    }


    public function setUpPipe(Closure $destination)
    {
        $this->pipeline = array_reduce(
            array_reverse($this->pipes), $this->carry(), $this->prepareDestination($destination)
        );
    }


    /**
     * Set the object being sent through the pipeline.
     *
     * @param  mixed  $passable
     * @return mixed
     */
    public function send($passable)
    {
        return call_user_func($this->pipeline, $passable);
    }


    /**
     * Get the container instance.
     *
     * @return ChatbotApp
     *
     * @throws \RuntimeException
     */
    protected function getContainer()
    {
        if (! $this->ioc) {
            throw new \RuntimeException('A container instance has not been passed to the Pipeline.');
        }

        return $this->ioc;
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
     * Get the final piece of the Closure onion.
     *
     * @param  \Closure  $destination
     * @return \Closure
     */
    protected function prepareDestination(Closure $destination)
    {
        return function ($passable) use ($destination) {
            return $destination($passable);
        };
    }


    /**
     * Parse full pipe string to get name and parameters.
     *
     * @param  string $pipe
     * @return array
     */
    protected function parsePipeString($pipe)
    {
        [$name, $parameters] = array_pad(explode(':', $pipe, 2), 2, []);

        if (is_string($parameters)) {
            $parameters = explode(',', $parameters);
        }

        return [$name, $parameters];
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
                    return $pipe($passable, $stack);
                } elseif (! is_object($pipe)) {
                    [$name, $parameters] = $this->parsePipeString($pipe);

                    // If the pipe is a string we will parse the string and resolve the class out
                    // of the dependency injection container. We can then build a callable and
                    // execute the pipe function giving in the parameters that are required.
                    $pipe = $this->getContainer()->make($name);

                    $parameters = array_merge([$passable, $stack], $parameters);
                } else {
                    // If the pipe is already an object we'll just make a callable and pass it to
                    // the pipe as-is. There is no need to do any extra parsing and formatting
                    // since the object we're given was already a fully instantiated object.
                    $parameters = [$passable, $stack];
                }

                return method_exists($pipe, $this->method)
                    ? $pipe->{$this->method}(...$parameters)
                    : $pipe(...$parameters);
            };
        };
    }


}