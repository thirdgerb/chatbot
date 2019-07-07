<?php


namespace Commune\Chatbot\OOHost\Session;

use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Contracts\CacheAdapter;
use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Exceptions\LogicException;
use Commune\Chatbot\OOHost\Context\Context;

use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\DialogImpl;

use Commune\Chatbot\OOHost\Directing\Dialog\Hear;
use Commune\Chatbot\OOHost\Directing\Director;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\History\History;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\Config\Host\OOHostConfig;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;
use Psr\Log\LoggerInterface;


/**
 * Class SessionImpl
 * @package Commune\Chatbot\OOHost\Session
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $sessionId
 * @property-read IncomingMessage $incomingMessage
 * @property-read Conversation $conversation
 * @property-read Scope $scope
 * @property-read IntentRegistrar $intentRepo
 * @property-read SessionLogger $logger
 * @property-read Repository $repo
 * @property-read Dialog $dialog
 * @property-read ChatbotConfig $chatbotConfig
 * @property-read OOHostConfig $hostConfig
 */
class SessionImpl implements Session, HasIdGenerator
{
    use IdGeneratorHelper, RunningSpyTrait;

    /**
     * @var bool
     */
    protected $heard = false;

    /**
     * @var bool
     */
    protected $quit = false;

    /*----- components -----*/

    /**
     * @var string;
     */
    protected $sessionId;

    /**
     * @var Conversation
     */
    protected $conversation;

    /**
     * @var Repository
     */
    protected $repo;

    /**
     * @var OOHostConfig
     */
    protected $hostConfig;

    /**
     * @var ChatbotConfig
     */
    protected $chatbotConfig;

    /**
     * @var \Closure
     */
    protected $rootContextMaker;

    /**
     * @var Driver
     */
    protected $driver;

    /**
     * @var CacheAdapter
     */
    protected $cache;

    /**
     * @var string
     */
    protected $cacheKey;

    /*----- cached -----*/

    /**
     * @var History
     */
    protected $history;

    /**
     * @var IncomingMessage
     */
    protected $incomingMessage;

    /**
     * @var SessionLogger
     */
    protected $logger;

    /**
     * @var Scope
     */
    protected $scope;

    /**
     * @var IntentMessage
     */
    protected $matchedIntent;

    /**
     * @var DialogImpl
     */
    protected $dialog;

    /**
     * @var bool
     */
    protected $sneak = false;

    /**
     * @var string
     */
    protected $traceId;

    /**
     * @var SessionMemory
     */
    protected $memory;


    public function __construct(
        string $belongsTo,
        CacheAdapter $cache,
        Conversation $conversation,
        Driver $driver,
        \Closure $rootContextMaker = null
    )
    {

        $this->conversation = $conversation;

        $this->traceId = $conversation->getTraceId();
        $this->chatbotConfig = $conversation->getChatbotConfig();
        $this->hostConfig = $this->chatbotConfig->host;

        $this->cache = $cache;
        $this->driver = $driver;

        $this->rootContextMaker = $rootContextMaker;
        // host config 没有强类型约束. 注意格式要正确.

        $this->prepareRepo($belongsTo, $driver);
        static::addRunningTrace($this->traceId, $this->sessionId);
    }

    protected function prepareRepo(
        string $belongsTo,
        Driver $driver
    ) : void
    {
        $this->cacheKey = "chatbot:session:snapshot:$belongsTo";
        $snapshot = $this->getSnapshot($this->cacheKey);
        $this->sessionId = $snapshot->sessionId;
        $this->repo = new Repository($this, $driver, $snapshot);
    }

    protected function getSnapshot(string $key) : Snapshot
    {
        $cached = $this->cache->get($key);

        if (!empty($cached)) {
            $us = unserialize($cached);
            // 如果 snapshot 的 saved 为false,
            // 说明出现重大错误, 导致上一轮没有saved.
            // 这时必须从头开始, 否则永远卡在错误这里.
            if ($us instanceof Snapshot && $us->saved) {
                // 新的一轮, snapshot saved 自然从头开始.
                $us->saved = false;
                return $us;
            }
        }

        return new Snapshot($this->createUuId());
    }


    /**
     * 专门给 director 用
     */
    public function shouldQuit() : void
    {
        $this->quit = true;

    }

    public function hear(Message $message, Navigator $navigator = null): void
    {
        if (!isset($navigator)) {
            $navigator = new Hear(
                $this->getDialog(),
                $this->getHistory(),
                $message
            );
        }

        $director = new Director($this);
        $this->heard = $director->hear($navigator);
    }

    public function isHeard(): bool
    {
        return $this->heard;
    }

    public function isQuiting(): bool
    {
        return $this->quit;
    }


    public function makeRootContext() : Context
    {
        if (isset($this->rootContextMaker)) {
            return call_user_func($this->rootContextMaker);
        }

        // 基于 registrar 来生成.
        $repo = ContextRegistrar::getIns();
        $name = $this->hostConfig->rootContextName;
        if ($repo->has($name)) {
            return $repo->get($name)->newContext()->toInstance($this);
        }

        throw new ConfigureException(
            static::class
            . ' can not instance root context '
            . $name
        );
    }


    public function newSession(string $belongsTo, \Closure $rootMaker): Session
    {
        return new SessionImpl(
            $belongsTo,
            $this->cache,
            $this->conversation,
            $this->driver,
            $rootMaker
        );
    }


    /*----------- cached value ------------*/

    /**
     * @return DialogImpl
     */
    public function getDialog() : Dialog
    {
        return $this->dialog ?? $this->dialog = new DialogImpl($this, $this->getHistory());
    }

    public function getScope() : Scope
    {
        return $this->scope
            ?? $this->scope = Scope::make(
                $this->sessionId,
                $this->conversation
            );
    }

    public function getLogger() : SessionLogger
    {
        $logger = $this->conversation->getLogger();
        return $this->logger
            ?? $this->logger = $this->conversation->make(
                SessionLogger::class,
                [
                    'logger' => $logger,
                    LoggerInterface::class => $logger,
                    'session' => $this,
                    Session::class => $this,
                ]
            );
    }

    public function getHistory() : History
    {
        return $this->history ?? $this->history = new History($this);
    }

    public function getIncomingMessage() : IncomingMessage
    {
        return $this->incomingMessage
                    ?? $this->conversation->getIncomingMessage();
    }

    public function getMemory() : SessionMemory
    {
        return $this->memory ?? $this->memory = new SessionMemory($this);
    }

    /*----------- intent ------------*/

    public function setMatchedIntent(IntentMessage $intent): void
    {
        if (!$intent->isInstanced()) {
            $intent = $intent->toInstance($this);
        }
        $this->matchedIntent = $intent;
    }

    public function getMatchedIntent(): ? IntentMessage
    {
        return $this->matchedIntent;
    }


    /*----------- get ------------*/

    public function __get($name)
    {
        switch($name) {
            case 'sessionId' :
                return $this->sessionId;
            case 'conversation' :
                return $this->conversation;
            case 'repo' :
                return $this->repo;
            case 'chatbotConfig' :
                return $this->chatbotConfig;
            case 'hostConfig' :
                return $this->hostConfig;
            case 'logger' :
                return $this->getLogger();


            case 'intentRepo' :
                return IntentRegistrar::getIns();
            case 'contextRepo' :
                return ContextRegistrar::getIns();

            case 'dialog' :
                return $this->getDialog();
            case 'incomingMessage' :
                return $this->getIncomingMessage();
            case 'scope' :
                return $this->getScope();
            case 'memory' :
                return $this->getMemory();
            default:
                return null;
        }
    }

    public function beSneak(): void
    {
        $this->sneak = true;
        $this->heard = true;
    }

    public function finish(): void
    {
        if ($this->isQuiting()) {
            // 也不保存了.
            $this->cache->forget($this->cacheKey);
            //$this->flush();
            return;
        }

        // 如果是sneak, 什么也不做. 也不会存储.
        if (!$this->sneak) {
            try {

                $snapshot = $this->repo->snapshot;
                $snapshot->saved = true;
                $this->saveCached($snapshot);

                // breakpoint
                $this->driver->saveBreakpoint(
                    $this,
                    $snapshot->breakpoint
                );
                $this->saveSnapshot($snapshot);
                $this->logTracking();

                $snapshot = null;


            // 存储失败.
            } catch (\Exception $e) {
                $this->cache->forget($this->cacheKey);
                $this->flush();
                throw new LogicException('finish session failure', $e);
            }
        }

        $this->flush();
    }

    public function flush() : void
    {
        // session 垃圾回收不掉时 写的排查代码.
        // 不删了, 通过它们容易暴露问题.
        $this->dialog = null;
        $this->repo = null;
        $this->driver = null;
        $this->cache = null;
        $this->matchedIntent = null;
        $this->incomingMessage = null;
        $this->scope = null;
        $this->history = null;
        $this->rootContextMaker = null;
        $this->hostConfig = null;
        $this->chatbotConfig = null;
        $this->conversation = null;
        $this->memory = null;
        $this->logger = null;
    }

    protected function logTracking() : void
    {
        $this->getLogger()->info(
            'session tracking',
            $this->getHistory()->tracker->tracking
        );
    }

    protected function saveSnapshot(Snapshot $snapshot) : void
    {
        // cache 快照
        $this->cache->set(
            $this->cacheKey,
            serialize($snapshot),
            $this->hostConfig->sessionExpireSeconds
        );
    }

    protected function saveCached(Snapshot $snapshot) : void
    {
        // 保存所有的信息.
        // 目前只有context
        foreach ($snapshot->cachedSessionData as $type => $typeData) {
            foreach ($typeData as $id => $data) {

                switch($type) {
                    case SessionData::CONTEXT_TYPE:
                        /**
                         * @var Context $data
                         */
                        if ($data->shouldSave()) {
                            $this->driver->saveContext($this, $data);
                        }
                }
            }
        }

    }

    public function __destruct()
    {
        self::removeRunningTrace($this->traceId);
    }

}