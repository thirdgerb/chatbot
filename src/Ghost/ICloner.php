<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost;

use Commune\Blueprint\Configs\GhostConfig;
use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Framework\Auth\Authority;
use Commune\Blueprint\Ghost;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Contracts\Cache;
use Commune\Contracts\Trans\SelfTranslatable;
use Commune\Contracts\Trans\Translator;
use Commune\Framework\ASession;
use Commune\Protocals\Comprehension;
use Commune\Protocals\Intercom\OutputMsg;
use Psr\Log\LoggerInterface;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Protocals\IntercomMsg;
use Commune\Support\Registry\OptRegistry;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\Session\SessionStorage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ICloner extends ASession implements Cloner
{
    const SINGLETONS =  [
        'logger' => Cloner\ClonerLogger::class,
        'storage' => Cloner\ClonerStorage::class,

        'input' => InputMsg::class,
        'comprehension' => Comprehension::class,
        'scene' => Cloner\ClonerScene::class,
        'scope' => Cloner\ClonerScope::class,
        'matcher' => Ghost\Tools\Matcher::class,
        'avatar' => Cloner\ClonerAvatar::class,

        'cache' => Cache::class,
        'auth' => Authority::class,

        'mind' => Ghost\Mindset::class,
        'runtime' => Ghost\Runtime\Runtime::class,

        'registry' => OptRegistry::class,
    ];

    /*------- components -------*/

    /**
     * @var Ghost
     */
    protected $_ghost;

    /**
     * @var GhostConfig
     */
    protected $_config;

    /**
     * @var string
     */
    protected $inputConvoId;

    /*------- cached -------*/

    /**
     * @var int
     */
    protected $expire;

    /**
     * @var IntercomMsg[]
     */
    protected $outputs = [];

    /**
     * @var InputMsg[]
     */
    protected $asyncInputs = [];

    /**
     * @var InputMsg[]
     */
    protected $asyncDeliveries = [];

    /**
     * @var bool
     */
    protected $quit = false;


    /**
     * @var bool
     */
    protected $stateless = false;

    /**
     * @var bool
     */
    protected $noConversationState = false;

    /**
     * @var Translator
     */
    protected $translator;

    public function __construct(
        Ghost $ghost,
        ReqContainer $container,
        string $sessionId,
        string $convoId = null
    )
    {
        $this->_ghost = $ghost;
        $this->_config = $ghost->getConfig();
        $this->inputConvoId = $convoId ?? '';

        // expire
        $this->expire = $this->_config->sessionExpire;
        parent::__construct($container, $sessionId);
    }

    public function getApp(): App
    {
        return $this->_ghost;
    }


    /*-------- session 的状态. 完全无状态的, session 不会读取状态相关的缓存 ---------*/

    public function noState(): void
    {
        $this->stateless = true;
    }

    public function isStateless(): bool
    {
        return $this->stateless;
    }

    /*-------- conversation id ---------*/

    public function getConversationId() : string
    {
        if (isset($this->conversationId)) {
            return $this->conversationId;
        }

        if ($this->isStateless()) {
            $convoId = $this->inputConvoId;
            $convoId = empty($convoId)
                ? $this->makeConvoId()
                : $convoId;

            return $this->conversationId = $convoId;
        }

        $inputCid = $this->inputConvoId;
        $cachedCid = $this->getConvoIdFromCache();

        // 如果没有缓存, 重新生成一个
        if (empty($cachedCid)) {
            $convoId = empty($inputCid) ? $this->makeConvoId() : $inputCid;

        // 有缓存的情况下, 可以被inputSid 覆盖.
        } elseif(empty($inputCid)) {
            $convoId = $cachedCid;
        } else {
            $convoId = $inputCid;
        }

        return $this->conversationId = $convoId;
    }

    protected function makeConvoId() : string
    {
        $clonerId = $this->getSessionId();
        $traceId = $this->_container->getId();
        return sha1("cloner:$clonerId:req:$traceId");
    }

    protected function getConvoIdFromCache() : ? string
    {
        $key = $this->getConvoCacheKey();
        return $this->__get('cache')->get($key);
    }

    protected function cacheConvoId(string $convoId) : void
    {
        $key = $this->getConvoCacheKey();
        $this->__get('cache')->set($key, $convoId, $this->getSessionExpire());
    }

    protected function ioDeleteConvoIdCache() : void
    {
        $key = $this->getConvoCacheKey();
        $this->__get('cache')->forget($key);
    }

    protected function getConvoCacheKey() : string
    {
        $cloneId = $this->getSessionId();
        return "clone:$cloneId:convo:id";
    }

    /*-------- properties ---------*/


    public function getStorage(): SessionStorage
    {
        return $this->__get('storage');
    }

    public function getLogger(): LoggerInterface
    {
        return $this->__get('logger');
    }

    /*------- getter -------*/

    public function __get($name)
    {
        switch ($name) {
            case 'ghost' :
                return $this->_ghost;
            case 'container' :
                return $this->_container;
            case 'config' :
                return $this->_config;
            default:
                return parent::__get($name);
        }
    }

    /*------- cache -------*/

    protected function getGhostClonerLockerKey() : string
    {
        $ghostId = $this->getAppId();
        $clonerId = $this->getSessionId();
        return "ghost:$ghostId:clone:$clonerId:locker";
    }

    public function lock(int $second): bool
    {
        $ttl = $this->_config->sessionLockerExpire;
        if ($ttl > 0) {
            $locker = $this->getGhostClonerLockerKey();
            return $this->__get('cache')->lock($locker, $ttl);
        } else {
            return true;
        }

    }

    public function isLocked(): bool
    {
        $ttl = $this->_config->sessionLockerExpire;
        if ($ttl > 0) {
            $locker = $this->getGhostClonerLockerKey();
            return $this->__get('cache')->has($locker);
        }
        return false;
    }

    public function unlock(): bool
    {
        $ttl = $this->_config->sessionLockerExpire;
        if ($ttl > 0) {
            $locker = $this->getGhostClonerLockerKey();
            return $this->__get('cache')->unlock($locker);
        }
        return true;
    }

    public function getSessionExpire(): int
    {
        return $this->expire;
    }

    public function setSessionExpire(int $seconds): void
    {
        $this->expire = $seconds;
    }

    /*------- output -------*/


    public function output(OutputMsg $output, OutputMsg ...$outputs): void
    {
        array_unshift($outputs, $output);
        foreach ($outputs as $output) {
            $message = $output->getMessage();
            if ($message instanceof SelfTranslatable) {
                $message->translate($this->getTranslator());
            }

            $this->outputs[] = $output;
        }
    }

    protected function getTranslator() : Translator
    {
        return $this->translator
            ?? $this->translator = $this->getContainer()
                ->make(Translator::class);
    }

    public function getOutputs(): array
    {
        return $this->outputs;
    }

    public function asyncInput(InputMsg $input, InputMsg ...$inputs): void
    {
        array_push($this->asyncInputs, $input, ...$inputs);
    }

    public function getAsyncInputs(): array
    {
        return $this->asyncInputs;
    }

    public function asyncDeliver(InputMsg $input, InputMsg ...$inputs): void
    {
        array_unshift($inputs, $input);
        array_push($this->asyncDeliveries, ...$inputs);
    }

    public function getAsyncDeliveries(): array
    {
        return $this->asyncDeliveries;
    }


    /*------- quit -------*/

    public function endConversation(): void
    {
        $this->quit = true;
        $this->noConversationState = true;
    }

    public function isConversationEnd(): bool
    {
        return $this->quit;
    }

    public function noConversationState(): void
    {
        $this->noConversationState = true;
    }


    /*------- flush -------*/

    protected function saveSession(): void
    {
        // 无状态的请求不做任何缓存.
        if ($this->isStateless()) {
            return;
        }

        if ($this->isConversationEnd()) {
            $this->ioDeleteConvoIdCache();
        }

        // storage 更新.
        if ($this->isSingletonInstanced('storage')) {
            $this->__get('storage')->save();
        }

        // 可以允许其它的状态不保存.
        if ($this->noConversationState) {
            return;
        }

        if ($this->isSingletonInstanced('runtime')) {
            $this->__get('runtime')->save();
        }

        // 更新 conversationId 缓存.
        if (isset($this->conversationId)) {
            $this->cacheConvoId($this->conversationId);
        }
    }


    protected function flushInstances(): void
    {
        unset(
            $this->_ghost,
            $this->_config,
            $this->outputs,
            $this->asyncInputs,
            $this->translator
        );
    }

}