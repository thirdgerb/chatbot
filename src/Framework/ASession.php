<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework;

use Commune\Support\Protocal\HandlerOption;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\Session;
use Commune\Framework\Exceptions\SerializeForbiddenException;
use Commune\Framework\Spy\SpyAgency;
use Commune\Support\Pipeline\OnionPipeline;
use Commune\Support\Protocal\Protocal;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ASession implements Session, HasIdGenerator
{
    use IdGeneratorHelper;

    const SINGLETONS = [
    ];

    /**
     * @var ReqContainer
     */
    protected $container;

    /**
     * @var string
     */
    protected $sessionId;

    /*------ cached ------*/

    /**
     * @var string[]
     */
    protected $listened = [];

    /**
     * Session 级别的单例.
     * @var array
     */
    protected $singletons = [];

    /**
     * @var string
     */
    protected $traceId;

    /**
     * @var bool
     */
    protected $finished = false;

    /**
     * @var bool
     */
    protected $stateless = false;

    /**
     * @var HandlerOption[][]
     */
    protected $protocalMap;

    /**
     * ASession constructor.
     * @param ReqContainer $container
     * @param string $sessionId
     */
    public function __construct(ReqContainer $container, string $sessionId = null)
    {
        $this->container = $container;
        $this->traceId = $container->getId();
        // 允许为 null
        $this->sessionId = $sessionId ?? $this->createUuId();
        SpyAgency::incr(static::class);
    }

    /*------ id ------*/

    public function getId(): string
    {
        return $this->sessionId;
    }


    /*------ abstract ------*/

    abstract protected function flushInstances() : void;

    abstract protected function saveSession() : void;

    /*------ components ------*/

    public function getContainer(): ReqContainer
    {
        return $this->container;
    }


    /*------ logic ------*/


    public function buildPipeline(array $pipes, string $via, \Closure $destination): \Closure
    {
        $pipeline = new OnionPipeline($this->container);
        $pipeline->through(...$pipes);
        $pipeline->via($via);
        return $pipeline->buildPipeline($destination);
    }

    public function getProtocalHandler(string $group, Protocal $protocalInstance): ? callable
    {
        if (!isset($this->protocalMap)) {
            $options = $this->getHandlerOptions();
            foreach ($options as $option) {
                $this->protocalMap[$option->group][$option->protocal] = $option;
            }
        }

        $options = $this->protocalMap[$group] ?? [];

        if (empty($options)) {
            return null;
        }

        foreach ($options as $name => $option) {
            $protocal = $option->protocal;
            if (is_a($protocalInstance, $protocal, TRUE)) {
                $abstract = $option->handler;
                $params = $option->params;
                $handlerIns = $this->getContainer()->make($abstract, $params);
                return $handlerIns;
            }
        }

        return null;
    }

    /**
     * @return HandlerOption[]
     */
    abstract protected function getHandlerOptions() : array;


    /*------ status ------*/

    public function getTraceId(): string
    {
        return $this->traceId;
    }

    public function getAppId(): string
    {
        return $this->getApp()->getId();
    }

    public function isFinished(): bool
    {
        return $this->finished;
    }

    /*------ event ------*/

    public function fire(Session\SessionEvent $event): void
    {
        $id = $event->getEventName();
        if (!isset($this->listened[$id])) {
            return;
        }

        // 执行所有的事件.
        foreach ($this->listened[$id] as $handler) {

            call_user_func($handler, $this, $event);
        }
    }

    public function listen(string $eventName, callable $handler): void
    {
        $this->listened[$eventName][] = $handler;
    }


    public function noState(): void
    {
        $this->stateless = true;
    }

    public function isStateless(): bool
    {
        return $this->stateless;
    }


    /*------ getter ------*/

    public function __get($name)
    {
        if ($name === 'container') {
            return $this->container;
        }

        $injectable = static::SINGLETONS[$name] ?? null;

        if (!empty($injectable)) {
            return $this->singletons[$name]
                ?? $this->singletons[$name] = $this->container->get($injectable);
        }

        return null;
    }

    protected function isSingletonInstanced($name) : bool
    {
        $injectable = static::SINGLETONS[$name] ?? null;

        return isset($injectable)
            ? isset($this->singletons[$name])
            : false;
    }

    public function __isset($name)
    {
        return isset(static::SINGLETONS[$name]);
    }

    /*------ finish ------*/

    public function finish(): void
    {
        if (!$this->isStateless()) {
            $this->saveSession();
        }

        unset($this->listened);
        unset($this->singletons);
        unset($this->finished);
        unset($this->protocalMap);
        $this->flushInstances();

        // container
        $container = $this->container;
        unset($this->container);
        $container->destroy();
    }


    public function __sleep()
    {
        throw new SerializeForbiddenException(static::class);
    }


    public function __destruct()
    {
        SpyAgency::decr(static::class);
    }
}