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
use Commune\Support\Utils\StringUtils;
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
    protected $sneaky = false;

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
        string $sessionId,
        OOHostConfig $hostConfig,
        Conversation $conversation,
        Driver $driver
    )
    {
        $this->hostConfig = $hostConfig;
        $this->conversation = $conversation;
        $this->sessionId = $sessionId;

        $this->traceId = $conversation->getTraceId();
        $this->repo = new RepositoryImpl($this->traceId, $sessionId, $driver);

        $this->chatbotConfig = $conversation->getChatbotConfig();
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
        $scene = $this->conversation->getRequest()->getScene();

        // 根据场景决定是否用不同的根路径.
        $sceneContextNames = $this->hostConfig->sceneContextNames;
        if (isset($scene) && isset($sceneContextNames[$scene])) {
            $name = $sceneContextNames[$scene];
        } else {
            $name = $this->hostConfig->rootContextName;
        }

        // 基于 registrar 来生成.
        $repo = $this->getContextRegistrar();
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
                $this->conversation
            );
    }

    public function getLogger() : SessionLogger
    {
        if (isset($this->logger)) {
            return $this->logger;
        }

        $logger = $this->conversation->getLogger();
        $this->logger = new SessionLogger($logger, $this);
        return $this->logger;
    }

    public function getRootHistory() : History
    {
        return $this->rootHistory
            ?? $this->rootHistory = new History($this, $this->sessionId, [$this, 'makeRootContext']);
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
        if (
            isset($this->matchedIntent)
            && array_key_exists($this->matchedIntent, $this->possibleIntents)
        ) {
            return $this->possibleIntents[$this->matchedIntent];
        }

        // 已经运行过, 说明没有匹配到过
        if ($this->intentMatchingTried) {
            return null;
        }

        // 只匹配一次
        // try matching once
        $this->intentMatchingTried = true;

        // 用默认的规范匹配.
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
        $repo = $this->getIntentRegistrar();
        // 对 intent name 进行标准化.
        if ($repo->hasDef($intentName)) {
            $intentName = $repo->getDef($intentName)->getName();
        } else {
            $intentName = StringUtils::normalizeContextName($intentName);
        }

        if (array_key_exists($intentName, $this->possibleIntents)) {
            return $this->possibleIntents[$intentName];
        }

        // 防止 matchedIntent 没有执行过.
        $matched = $this->getMatchedIntent();
        if (isset($matched) && $matched->nameEquals($intentName)) {
            return $matched;
        }

        // 执行主动匹配逻辑.
        $intent = $repo->matchCertainIntent($intentName, $this);

        // 缓存环节.
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
        $this->sneaky = true;
        $this->handled = true;
    }

    public function isSneaky(): bool
    {
        return $this->sneaky;
    }


    public function finish(): void
    {
        // 如果是sneak, 什么也不做. 也不会存储.
        if (!$this->sneaky) {
            try {
                $this->repo->save($this);
                $this->logTracking();

            // 存储失败.
            } catch (\Exception $e) {
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