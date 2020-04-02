<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Prototype\Session;

use Commune\Framework\Blueprint\Intercom\GhostInput;
use Commune\Framework\Blueprint\ReqContainer;
use Commune\Framework\Contracts;
use Commune\Framework\Blueprint\Intercom\ShellInput;
use Commune\Framework\Blueprint\Intercom\ShellOutput;
use Commune\Shell\Blueprint\Event\ShellEvent;
use Commune\Shell\Blueprint\Session\ShlSession;
use Commune\Shell\Blueprint\Session\ShlSessionLogger;
use Commune\Shell\Blueprint\Session\ShlSessionStorage;
use Commune\Shell\Blueprint\Shell;
use Commune\Shell\Contracts\ShlRequest;
use Commune\Shell\Contracts\ShlResponse;
use Commune\Support\RunningSpy\Spied;
use Commune\Support\RunningSpy\SpyTrait;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 */
class IShlSession implements ShlSession, HasIdGenerator, Spied
{
    use IdGeneratorHelper, SpyTrait;

    const INJECTABLE_PROPERTIES = [
        'cache' => Contracts\Cache::class,
        'messenger' => Contracts\Messenger::class,
        'storage' => ShlSessionStorage::class,
        'logger' => ShlSessionLogger::class,
        'request' => ShlRequest::class,
        'response' => ShlResponse::class,
        'shell' => Shell::class,
        'shellInput' => ShellInput::class,
        'ghostInput' => GhostInput::class
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
     * @var ShellOutput[]
     */
    protected $outputs = [];

    /**
     * @var string
     */
    protected $traceId;

    /**
     * @var bool
     */
    protected $finished = false;

    /**
     * @var string
     */
    protected $chatId;

    /**
     * @var ShellInput
     */
    protected $shellInput;

    /*------ construct ------*/

    /**
     * IShlSession constructor.
     * @param ReqContainer $container
     */
    public function __construct(ReqContainer $container)
    {
        $this->container = $container;

        $request = $this->request;
        $this->traceId = $request->getTraceId();
        static::addRunningTrace($this->traceId, $this->traceId);
    }


    /*------ status ------*/

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

    /*------ i/o ------*/

    /**
     * 生成 chatId
     * @return string
     */
    public function getChatId(): string
    {
        return $this->chatId
            ?? $this->chatId = (
                $this->request->getChatId()
                ?? sha1(
                    'shell:'
                    . $this->shell->getShellName()
                    . ':user:'
                    . $this->request->getUserId()
                )
            );
    }

    public function setShellOutputs(array $outputs): void
    {
        $this->outputs = $outputs;
    }

    public function getShellOutputs(): array
    {
        return $this->outputs;
    }


    /*------ event ------*/

    public function fire(ShellEvent $event): void
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
        $this->storage->save();
        $this->container = null;
        $this->properties = [];
        $this->listened = [];
        $this->outputs = [];
        $this->finished = true;
    }

    public function __destruct()
    {
        static::removeRunningTrace($this->traceId);
    }


}