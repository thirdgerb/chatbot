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
use Commune\Chatbot\Framework\Exceptions\RuntimeException;
use Commune\Chatbot\OOHost\Context\Context;

use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\Memory\MemoryRegistrar;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\DialogImpl;

use Commune\Chatbot\OOHost\Directing\Director;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\History\History;
use Commune\Chatbot\Config\Children\OOHostConfig;
use Commune\Chatbot\OOHost\History\Tracker;
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
    protected $handled = false;

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

    /*----- cached -----*/

    /**
     * @var bool
     */
    protected $intentMatchingTried = false;

    /**
     * @var History
     */
    protected $rootHistory;

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
     * @var string|null
     */
    protected $matchedIntent;

    /**
     * @var IntentMessage[]
     */
    protected $possibleIntents = [];

    /**
     * @var DialogImpl
     */
    protected $rootDialog;

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
        Repository $repository
    )
    {
        $this->belongsTo = $belongsTo;
        $this->hostConfig = $hostConfig;
        $this->conversation = $conversation;
        $this->repo = $repository;


        $this->traceId = $conversation->getTraceId();
        $this->chatbotConfig = $conversation->getChatbotConfig();
        $snapshot = $this->repo->getSnapshot($belongsTo);
        $this->sessionId = $snapshot->sessionId;
        $this->tracker = new Tracker($this->sessionId);

        static::addRunningTrace($this->traceId, $this->sessionId);
    }



    /**
     * 专门给 director 用
     */
    public function shouldQuit() : void
    {
        $this->quit = true;
    }

    public function handle(Message $message, Navigator $navigator = null): void
    {
        $director = new Director($this);
        $this->handled = $director->handle($navigator);
    }

    public function isHandled(): bool
    {
        return $this->handled;
    }

    public function isQuiting(): bool
    {
        return $this->quit;
    }


    public function makeRootContext() : Context
    {
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

    /*----------- cached value ------------*/

    /**
     * @return DialogImpl
     */
    public function getRootDialog() : Dialog
    {
        return $this->rootDialog
            ?? $this->rootDialog = new DialogImpl(
                $this,
                $this->getRootHistory(),
                $this->getIncomingMessage()->getMessage()
            );
    }

    public function getScope() : Scope
    {
        return $this->scope
            ?? $this->scope = Scope::make(
                $this->sessionId,
                $this->belongsTo,
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

    public function getRootHistory() : History
    {
        return $this->rootHistory
            ?? $this->rootHistory = new History($this, $this->belongsTo,[$this, 'makeRootContext']);
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
        $this->matchedIntent = $intent->getName();
        $this->setPossibleIntent($intent);
    }

    public function getMatchedIntent(): ? IntentMessage
    {
        if (isset($this->matchedIntent)) {
            return $this->getPossibleIntent($this->matchedIntent);
        }

        if ($this->intentMatchingTried) {
            return null;
        }

        // try matching once, still could set matched intent
        $this->intentMatchingTried = true;
        $message = $this->getIncomingMessage()->message;
        if ($message instanceof IntentMessage) {
            $this->setPossibleIntent($message);
            return $this->matchedIntent = $message->getName();
        }

        $intent = $this->getIntentRegistrar()->matchIntent($this);
        if (isset($intent)) {
            $this->setPossibleIntent($intent);
            $this->matchedIntent = $intent->getName();
        }
        return $intent;
    }

    public function setPossibleIntent(IntentMessage $intent): void
    {
        if (!$intent->isInstanced()) {
            $intent = $intent->toInstance($this);
        }
        $this->possibleIntents[$intent->getName()] = $intent;
    }

    public function getPossibleIntent(string $intentName): ? IntentMessage
    {
        if (array_key_exists($intentName, $this->possibleIntents)) {
            return $this->possibleIntents[$intentName];
        }

        // 防止 matchedIntent 没有执行过.
        $matched = $this->getMatchedIntent();
        if (isset($matched) && $matched->nameEquals($intentName)) {
            $this->setPossibleIntent($matched);
            return $matched;
        }

        // 执行主动匹配逻辑.
        $intent = $this->getIntentRegistrar()->matchCertainIntent($intentName, $this);
        if (isset($intent)) {
            $this->setPossibleIntent($intent);
        } else {
            $this->possibleIntents[$intentName] = null;
        }

        return $intent;
    }


    /*----------- get ------------*/

    public function __get($name)
    {
        switch($name) {

            // 直接可取的.
            case 'belongsTo' :
                return $this->belongsTo;
            case 'sessionId' :
                return $this->sessionId;
            case 'conversation' :
                return $this->conversation;
            case 'repo' :
                return $this->repo;
            case 'tracker':
                return $this->tracker;
            case 'chatbotConfig' :
                return $this->chatbotConfig;
            case 'hostConfig' :
                return $this->hostConfig;


            // 过程中生成的.
            case 'logger' :
                return $this->getLogger();
            case 'intentRepo' :
                return $this->getIntentRegistrar();
            case 'contextRepo' :
                return $this->getContextRegistrar();
            case 'memoryRepo' :
                return $this->getMemoryRegistrar();
            case 'dialog' :
                return $this->getRootDialog();
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
        $this->handled = true;
    }

    public function finish(): void
    {
        // 如果是sneak, 什么也不做. 也不会存储.
        if (!$this->sneak) {
            try {
                $this->repo->flush($this);
                $this->logTracking();

            // 存储失败.
            } catch (\Exception $e) {
                $this->repo->getDriver()->clearSnapshot($this->belongsTo);
                $this->flushProperties();
                throw new LogicException('finish session failure', $e);
            }
        }

        $this->flushProperties();
    }

    public function flushProperties() : void
    {
        // session 垃圾回收不掉时 写的排查代码.
        // 不删了, 通过它们容易暴露问题.
        $this->rootDialog = null;
        $this->repo = null;
        $this->matchedIntent = null;
        $this->possibleIntents = [];
        $this->incomingMessage = null;
        $this->scope = null;
        $this->rootDialog = null;
        $this->rootHistory = null;
        $this->hostConfig = null;
        $this->chatbotConfig = null;
        $this->conversation = null;
        $this->memory = null;
        $this->logger = null;
    }

    protected function logTracking() : void
    {
        if (!$this->hostConfig->logRedirectTracking ) {
            return;
        }

        $trackingData = $this->tracker->tracking;
        $count = count($trackingData);
        $tracking = implode('|', array_map(function($tracker){
            return json_encode($tracker);
        }, $trackingData));

        $this->getLogger()->info("sessionTracking $count times : $tracking");
    }

    public function __sleep()
    {
        throw new RuntimeException('try to serialize session which is forbidden, this occur usually because the serializing object use session as property');
    }


    public function __destruct()
    {
        self::removeRunningTrace($this->traceId);
    }

}