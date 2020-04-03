<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Prototype\Session;

use Commune\Framework\Blueprint\ChatApp;
use Commune\Framework\Blueprint\ReqContainer;
use Commune\Framework\Blueprint\Session\Session;
use Commune\Framework\Blueprint\Session\SessionEvent;
use Commune\Framework\Exceptions\SerializeSessionException;
use Commune\Support\RunningSpy\Spied;
use Commune\Support\RunningSpy\SpyTrait;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ASession implements Session, Spied
{
    use SpyTrait;

    const INJECTABLE_PROPERTIES = [
    ];

    /**
     * @var ReqContainer
     */
    protected $container;

    /*------ cached ------*/

    /**
     * @var array
     */
    protected $properties = [];


    /**
     * @var string[]
     */
    protected $listened = [];


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
    protected $silent = false;

    /**
     * @var ChatApp
     */
    protected $app;

    /**
     * IShlSession constructor.
     * @param ReqContainer $container
     */
    public function __construct(ReqContainer $container)
    {
        $this->container = $container;

        $this->traceId = $container->getId();
        static::addRunningTrace($this->traceId, $this->traceId);
    }

    /*------ abstract ------*/

    abstract protected function flush() : void;

    abstract protected function save() : void;

    /*------ components ------*/

    public function getContainer(): ReqContainer
    {
        return $this->container;
    }

    public function getApp(): ChatApp
    {
        return $this->app
            ?? $this->app = $this->container->make(ChatApp::class);
    }

    /*------ status ------*/

    public function getTraceId(): string
    {
        return $this->traceId;
    }


    public function isFinished(): bool
    {
        return $this->finished;
    }


    public function setProperty(string $name, $object): void
    {
        $abstract = static::INJECTABLE_PROPERTIES[$name] ?? null;
        if (empty($abstract) || !is_a($object, $abstract, TRUE)) {
            return;
        }

        $this->container->share($abstract, $object);
        $this->properties[$name] = $object;
    }

    /*------ event ------*/

    public function fire(SessionEvent $event): void
    {
        $id = $event->getId();
        if (!isset($this->listened[$id])) {
            return;
        }

        // 执行所有的事件.
        foreach ($this->listened[$id] as $handler) {
            $handler($this, $event);
        }
    }

    public function listen(string $eventName, callable $handler): void
    {
        $this->listened[$eventName][] = $handler;
    }

    /*------ silence ------*/

    public function silence(): void
    {
        $this->silent = true;
    }

    public function isSilent(): bool
    {
        return $this->silent;
    }


    /*------ getter ------*/

    public function __get($name)
    {
        if ($name === 'container') {
            return $this->container;
        }

        $injectable = static::INJECTABLE_PROPERTIES[$name] ?? null;
        if (!empty($injectable)) {
            return $this->properties[$name]
                ?? $this->properties[$name] = $this->container->get($injectable);
        }

        return null;
    }

    /*------ finish ------*/

    public function finish(): void
    {
        if (!$this->isSilent()) {
            $this->getStorage()->save();
            $this->save();
        }

        $this->container = null;
        $this->app = null;
        $this->properties = [];
        $this->listened = [];
        $this->flush();
        $this->finished = true;
    }


    public function __sleep()
    {
        throw new SerializeSessionException(static::class);
    }


    public function __destruct()
    {
        static::removeRunningTrace($this->traceId);
    }
}