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
use Commune\Blueprint\Ghost;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Contracts\Cache;
use Commune\Framework\ASession;
use Commune\Message\Host\SystemInt\SessionQuitInt;
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

        'scene' => Cloner\ClonerScene::class,
        'scope' => Cloner\ClonerScope::class,
        'matcher' => Ghost\Tools\Matcher::class,

        'cache' => Cache::class,
        'auth' => Ghost\Auth\Authority::class,

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
    protected $ghostConfig;

    /**
     * @var InputMsg
     */
    protected $_input;

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
     * @var bool
     */
    protected $quit = false;


    public function __construct(Ghost $ghost, ReqContainer $container, InputMsg $input)
    {
        $this->_ghost = $ghost;
        $this->ghostConfig = $ghost->getConfig();
        $this->_input = $input;
        $this->inputConvoId = $input->getConversationId();

        // expire
        $this->expire = $this->ghostConfig->sessionExpire;
        parent::__construct($container, $input->getSessionId());
    }

    public function getApp(): App
    {
        return $this->_ghost;
    }

    public function replaceInput(InputMsg $input): void
    {
        $this->container->share(InputMsg::class, $input);
        $this->_input = $input;
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
        $messageId = $this->_input->getMessageId();
        return sha1("cloner:$clonerId:message:$messageId");
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
        if ($name === 'ghost') {
            return $this->_ghost;
        }

        if ($name === 'config') {
            return $this->ghostConfig;
        }

        if ($name === 'input') {
            return $this->_input;
        }

        return parent::__get($name);
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
        $ttl = $this->ghostConfig->sessionLockerExpire;
        if ($ttl > 0) {
            $locker = $this->getGhostClonerLockerKey();
            return $this->__get('cache')->lock($locker, $ttl);
        } else {
            return true;
        }

    }

    public function isLocked(): bool
    {
        $ttl = $this->ghostConfig->sessionLockerExpire;
        if ($ttl > 0) {
            $locker = $this->getGhostClonerLockerKey();
            return $this->__get('cache')->has($locker);
        }
        return false;
    }

    public function unlock(): bool
    {
        $ttl = $this->ghostConfig->sessionLockerExpire;
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


    public function output(IntercomMsg $output, IntercomMsg ...$outputs): void
    {
        array_unshift($outputs, $output);
        $this->outputs = array_merge($this->outputs, $outputs);
//        $this->outputs = array_reduce(
//            $outputs,
//            function($outputs, IntercomMsg $output){
//                $outputs[] = $output;
//                return $outputs;
//            },
//            $this->outputs
//        );
    }

    public function getOutputs(): array
    {
        return $this->outputs;
    }

    public function asyncInput(InputMsg $input): void
    {
        $this->asyncInputs[] = $input;
    }

    public function getAsyncInputs(): array
    {
        return $this->asyncInputs;
    }

    /*------- quit -------*/

    public function endConversation(): void
    {
        $this->output(
            $this->_input->output(new SessionQuitInt())
        );

        $this->quit = true;
    }

    public function isConversationEnd(): bool
    {
        return $this->quit;
    }

    /*------- flush -------*/

    protected function flushInstances(): void
    {
        unset($this->_ghost);
        unset($this->ghostConfig);
        unset($this->_input);
        unset($this->outputs);
        unset($this->asyncInputs);
    }



    protected function saveSession(): void
    {
        if ($this->isConversationEnd()) {
            $this->ioDeleteConvoIdCache();
            return;
        }

        // runtime 更新.
        if ($this->isSingletonInstanced('runtime')) {
            $this->__get('runtime')->save();
        }

        // storage 更新.
        if ($this->isSingletonInstanced('storage')) {
            $this->__get('storage')->save();
        }

        // 更新 sessionId 缓存.
        if (isset($this->conversationId)) {
            $this->cacheConvoId($this->conversationId);
        }
    }

}