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

use Commune\Framework\Blueprint\App;
use Commune\Framework\Blueprint\ReqContainer;
use Commune\Framework\Blueprint\Server\Server;
use Commune\Framework\Blueprint\Session\Session;
use Commune\Framework\Blueprint\Session\SessionEvent;
use Commune\Framework\Exceptions\SerializeForbiddenException;
use Commune\Support\RunningSpy\Spied;
use Commune\Support\RunningSpy\SpyTrait;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ASession implements Session, Spied, HasIdGenerator
{
    use SpyTrait, IdGeneratorHelper;

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
    protected $uuid;

    /**
     * @var bool
     */
    protected $finished = false;

    /**
     * @var bool
     */
    protected $stateless = false;

    /**
     * @var App
     */
    protected $app;

    /**
     * IShlSession constructor.
     * @param ReqContainer $container
     */
    public function __construct(ReqContainer $container)
    {
        $this->container = $container;
        $this->uuid = $container->getUuid();
        // 初始化 stateless
        $this->stateless = $this->getRequest()->isStateless();

        static::addRunningTrace($this->uuid, $this->uuid);
    }

    /*------ abstract ------*/

    abstract protected function flushInstances() : void;

    abstract protected function saveSession() : void;

    /*------ components ------*/

    public function getContainer(): ReqContainer
    {
        return $this->container;
    }

    public function getApp(): App
    {
        return $this->app
            ?? $this->app = $this->container->make(App::class);
    }

    /*------ status ------*/

    public function getUuId(): string
    {
        return $this->uuid;
    }

    public function getChatId(): string
    {
        return $this->getRequest()->getChatId();
    }

    public function getServer(): Server
    {
        return $this->getApp()->getServer();
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
        if (!$this->isStateless()) {
            $this->saveSession();
            $this->getStorage()->save();
        }

        $this->container = null;
        $this->app = null;
        // important! otherwise object stored in the array wouldn't gc
        $this->properties = [];
        $this->listened = [];
        $this->flushInstances();
        $this->finished = true;
    }


    public function __sleep()
    {
        throw new SerializeForbiddenException(static::class);
    }


    public function __destruct()
    {
        static::removeRunningTrace($this->uuid);
    }
}