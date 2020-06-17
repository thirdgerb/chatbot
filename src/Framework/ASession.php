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

use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\Session;
use Commune\Framework\Exceptions\SerializeForbiddenException;
use Commune\Framework\Spy\SpyAgency;
use Commune\Support\Pipeline\OnionPipeline;
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
    protected $_container;

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
     * ASession constructor.
     * @param ReqContainer $container
     * @param string $sessionId
     */
    public function __construct(ReqContainer $container, string $sessionId = null)
    {
        $this->_container = $container;
        $this->traceId = $container->getId();
        // 允许为 null
        $this->sessionId = $sessionId ?? $this->createUuId();
        SpyAgency::incr(static::class);
    }

    /*------ id ------*/

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /*------ abstract ------*/

    abstract protected function flushInstances() : void;

    abstract protected function saveSession() : void;

    /*------ components ------*/

    public function getContainer(): ReqContainer
    {
        return $this->_container;
    }


    /*------ logic ------*/


    public function buildPipeline(array $pipes, string $via, \Closure $destination): \Closure
    {
        $pipeline = new OnionPipeline($this->_container);
        $pipeline->through(...$pipes);
        $pipeline->via($via);
        return $pipeline->buildPipeline($destination);
    }


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
        $injectable = static::SINGLETONS[$name] ?? null;

        if (!empty($injectable)) {
            return $this->singletons[$name]
                ?? $this->singletons[$name] = $this->_container->get($injectable);
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
        $this->flushInstances();

        // container
        $container = $this->_container;
        unset($this->_container);
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