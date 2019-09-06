<?php


namespace Commune\Chatbot\OOHost\Session;

use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Commune\Chatbot\Blueprint\Conversation\NLU;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Exceptions\LogicException;
use Commune\Chatbot\OOHost\Context\Context;

use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\Memory\MemoryRegistrar;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\DialogImpl;

use Commune\Chatbot\OOHost\Directing\Dialog\Hear;
use Commune\Chatbot\OOHost\Directing\Director;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\History\History;
use Commune\Chatbot\Config\Children\OOHostConfig;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;
use Psr\Log\LoggerInterface;


/**
 * @mixin Session
 */
class SessionImpl implements Session, HasIdGenerator
{
    use IdGeneratorHelper, RunningSpyTrait;


    /*----- property -----*/

    /**
     * @var bool
     */
    protected $heard = false;

    /**
     * @var bool
     */
    protected $quit = false;


    /**
     * @var string;
     */
    protected $sessionId;

    /**
     * @var string;
     */
    protected $belongsTo;

    /*----- components -----*/

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
     * @var NLU
     */
    protected $nlu;

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

    /**
     * @var IntentRegistrar;
     */
    protected $intentRepo;

    /**
     * @var ContextRegistrar
     */
    protected $contextRepo;

    /**
     * @var MemoryRegistrar
     */
    protected $memoryRepo;

    public function __construct(
        string $belongsTo,
        OOHostConfig $hostConfig,
        Conversation $conversation,
        Driver $driver,
        \Closure $rootContextMaker = null
    )
    {
        $this->belongsTo = $belongsTo;
        $this->hostConfig = $hostConfig;
        $this->conversation = $conversation;

        $this->traceId = $conversation->getTraceId();
        $this->chatbotConfig = $conversation->getChatbotConfig();

        $this->driver = $driver;
        $this->rootContextMaker = $rootContextMaker;

        $snapshot = $this->getSnapshot($belongsTo);
        $this->sessionId = $snapshot->sessionId;

        $this->repo = new Repository($this, $driver, $snapshot);

        static::addRunningTrace($this->traceId, $this->sessionId);
    }

    protected function getSnapshot(string $belongsTo) : Snapshot
    {
        $cached = $this->driver->findSnapshot($belongsTo);

        if (!empty($cached)) {
            // 如果 snapshot 的 saved 为false,
            // 说明出现重大错误, 导致上一轮没有saved.
            // 这时必须从头开始, 否则永远卡在错误这里.
            if ($cached instanceof Snapshot && $cached->saved) {
                $cached->saved = false;
                return $cached;
            }
        }

        return new Snapshot($belongsTo, $this->createUuId());
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
        $repo = $this->getContextRegistrar();
        $name = $this->hostConfig->rootContextName;
        if ($repo->hasDef($name)) {
            return $repo->getDef($name)->newContext()->toInstance($this);
        }

        throw new ConfigureException(
            static::class
            . ' can not instance root context '
            . $name
        );
    }


    public function newSession(string $belongsTo, \Closure $rootMaker, OOHostConfig $config = null): Session
    {
        return new SessionImpl(
            $belongsTo,
            $config ?? $this->hostConfig,
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

    public function getNLU() : NLU
    {
        return $this->nlu
            ?? $this->conversation->getNLU();
    }

    public function getMemory() : SessionMemory
    {
        return $this->memory ?? $this->memory = new SessionMemory($this);
    }

    public function getIntentRegistrar() : IntentRegistrar
    {
        return $this->intentRepo
            ?? $this->intentRepo = $this->conversation->get(IntentRegistrar::class);
    }

    public function getMemoryRegistrar() : MemoryRegistrar
    {
        return $this->memoryRepo
            ?? $this->memoryRepo = $this->conversation->get(MemoryRegistrar::class);
    }

    public function getContextRegistrar() : ContextRegistrar
    {
        return $this->contextRepo
            ?? $this->contextRepo = $this->conversation->get(ContextRegistrar::class);
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
        if (isset($this->matchedIntent)) {
            return $this->matchedIntent;
        }

        $message = $this->getIncomingMessage()->message;
        if ($message instanceof IntentMessage) {
            return $this->matchedIntent = $message;
        }

        return null;
    }


    /*----------- get ------------*/

    public function __get($name)
    {
        switch($name) {
            case 'belongsTo' :
                return $this->belongsTo;
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
                return $this->getIntentRegistrar();
            case 'contextRepo' :
                return $this->getContextRegistrar();
            case 'memoryRepo' :
                return $this->getMemoryRegistrar();

            case 'dialog' :
                return $this->getDialog();
            case 'incomingMessage' :
                return $this->getIncomingMessage();
            case 'nlu' :
                return $this->getNLU();
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
            $this->driver->clearSnapshot($this->belongsTo);
            //$this->flush();
            return;
        }

        // 如果是sneak, 什么也不做. 也不会存储.
        if (!$this->sneak) {
            try {

                $snapshot = $this->repo->snapshot;
                $snapshot->saved = true;

                $this->saveCachedContexts($snapshot);

                // breakpoint
                $this->driver->saveBreakpoint(
                    $this,
                    $snapshot->breakpoint
                );

                // snapshot & extend cache life
                $this->driver
                    ->saveSnapshot(
                        $snapshot,
                        $this->hostConfig->sessionExpireSeconds
                    );

                $this->logTracking();

                $snapshot = null;


            // 存储失败.
            } catch (\Exception $e) {
                $this->driver->clearSnapshot($this->belongsTo);
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
        $tracking = implode('|', array_map(function($tracker){
            return json_encode($tracker);
        }, $this->getHistory()->tracker->tracking));

        $this->getLogger()->info("sessionTracking $tracking");
    }


    protected function saveCachedContexts(Snapshot $snapshot) : void
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