<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Stage;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Definition\StageDef;
use Commune\Ghost\Blueprint\Runtime\Node;
use Commune\Ghost\Blueprint\Speak\Speaker;
use Commune\Ghost\Blueprint\Stage\Matcher;
use Commune\Ghost\Blueprint\Stage\Stage;
use Commune\Message\Blueprint\Message;
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
     * @var Conversation
     */
    protected $conversation;

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
     * AStage constructor.
     * @param Conversation $conversation
     * @param StageDef $stageDef
     * @param Node $self
     */
    public function __construct(
        Conversation $conversation,
        StageDef $stageDef,
        Node $self
    )
    {
        $this->conversation = $conversation;
        $this->uuid = md5($self->contextId . $stageDef->getFullname() . static::class);
        $this->stageDef = $stageDef;
        $this->selfNode = $self;
        static::addRunningTrace($this->uuid, $this->uuid);
    }

    public function matcher(Message $message = null): Matcher
    {
        $message = $message ?? $this->conversation->ghostInput->getMessage();
        return new IMatcher($this, $message);
    }

    public function speak(): Speaker
    {
        return $this->conversation->speaker;
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
        $injectable = [
            'stage' => $this,
            'conversation' => $this->conversation,
            'self' => $this->self,
            'node' => $this->selfNode,
            'message' => $this->conversation->ghostInput->getMessage()
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

        // 容器
        $container = $this->conversation->container;
        if (!is_callable($caller) && is_string($caller) && class_exists($caller)) {
            $caller = $container->make($caller, $parameters);
        }

        return $container->call($caller, $parameters);
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
                return $this->conversation;
            case 'def' :
                return $this->def;
            case 'self' :
                return $this->selfContext
                    ?? $this->selfContext = $this->selfNode->findContext($this->conversation);
            case 'node' :
                return $this->selfNode;
            default :
                return null;
        }
    }


    public function __destruct()
    {
        $this->conversation = null;
        $this->selfNode = null;
        $this->selfContext = null;
        $this->stageDef = null;
        static::removeRunningTrace($this->uuid);
    }

}