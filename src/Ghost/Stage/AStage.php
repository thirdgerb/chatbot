<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Stage;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Definition\StageDef;
use Commune\Blueprint\Ghost\Runtime\Node;
use Commune\Blueprint\Ghost\Stage\Matcher;
use Commune\Blueprint\Ghost\Stage\Stage;
use Commune\Protocals\HostMsg;
use Commune\Support\DI\Injectable;
use Commune\Support\DI\TInjectable;
use Commune\Support\RunningSpy\Spied;
use Commune\Support\RunningSpy\SpyTrait;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AStage implements Stage, Spied, Injectable
{
    use SpyTrait, TInjectable;

    protected $uuid;

    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * @var StageDef
     */
    protected $stageDef;

    /**
     * @var Node
     */
    protected $selfNode;


    /*------- cached -------*/

    /**
     * @var Context
     */
    protected $selfContext;

    /**
     * @var Matcher
     */
    protected $matcher;

    /**
     * AStage constructor.
     * @param Cloner $cloner
     * @param StageDef $stageDef
     * @param Node $self
     */
    public function __construct(
        Cloner $cloner,
        StageDef $stageDef,
        Node $self
    )
    {
        $this->cloner = $cloner;
        $this->uuid = md5($self->contextId . $stageDef->getFullname() . static::class);
        $this->stageDef = $stageDef;
        $this->selfNode = $self;
        static::addRunningTrace($this->uuid, $this->uuid);
    }

    public function matcher(HostMsg $message = null): Matcher
    {
        if (!isset($message) && isset($this->matcher)) {
            return $this->matcher;
        }

        $message = $message ?? $this->cloner->ghostInput->message;
        return $this->matcher = new IMatcher($this, $message);
    }


    public function make(string $abstract, array $parameters = [])
    {
        $parameters = $parameters + $this->getContextInjections();
        // 容器
        $container = $this->cloner->container;
        return $container->make($abstract, $parameters);
    }


    /**
     * @param callable|string $caller
     * @param array $parameters
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \ReflectionException
     */
    public function call($caller, array $parameters = [])
    {
        $parameters = $parameters + $this->getContextInjections();

        // 容器
        $container = $this->cloner->container;
        return $container->call($caller, $parameters);
    }

    public function getContextInjections(): array
    {
        $parameters = [];

        $injectable = [
            'stage' => $this,
            'conversation' => $this->cloner,
            'self' => $this->self,
            'node' => $this->selfNode,
            'message' => $this->cloner->ghostInput->getMessage(),
            'matcher' => $this->matcher(),
        ];

        // 准备好各种依赖注入.
        foreach ($injectable as $key => $object) {
            $parameters[$key] = $object;

            if ($object instanceof Injectable) {
                foreach ($object->getInterfaces() as $interface) {
                    $parameters[$interface] = $object;
                }
            }
        }

        // 可以用 $dependencies 来查看可以依赖注入的对象.
        $parameters['dependencies'] = array_keys($parameters);
        return $parameters;
    }

    public function getContextualInjections(): array
    {
        // TODO: Implement getContextualInjections() method.
    }


    public function getInterfaces(): array
    {
        return static::getInterfacesOf(Stage::class);
    }


    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        switch ($name) {
            case 'conversation' :
                return $this->cloner;
            case 'def' :
                return $this->def;
            case 'self' :
                return $this->selfContext
                    ?? $this->selfContext = $this->selfNode->findContext($this->cloner);
            case 'node' :
                return $this->selfNode;
            default :
                return null;
        }
    }


    public function __destruct()
    {
        $this->cloner = null;
        $this->selfNode = null;
        $this->selfContext = null;
        $this->stageDef = null;
        $this->matcher = null;
        static::removeRunningTrace($this->uuid);
    }

}