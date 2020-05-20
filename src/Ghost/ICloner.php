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
use Commune\Blueprint\Ghost;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
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
        'scope' => Cloner\ClonerScope::class,
        'config' => GhostConfig::class,
        'convo' => Ghost\Tools\Deliver::class,
        'cache' => Cache::class,
        'auth' => Ghost\Auth\Authority::class,
        'mind' => Ghost\Mindset::class,
        'runtime' => Ghost\Runtime\Runtime::class,
        'registry' => OptRegistry::class,
        'logger' => Cloner\ClonerLogger::class,
    ];

    /*------- components -------*/

    /**
     * @var Ghost
     */
    protected $ghost;

    /**
     * @var GhostConfig
     */
    protected $ghostConfig;

    /**
     * @var InputMsg
     */
    protected $input;

    /*------- cached -------*/

    /**
     * @var string
     */
    protected $clonerId;

    /**
     * @var int
     */
    protected $expire;

    /**
     * @var Context[]
     */
    protected $contexts = [];

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
    protected $silent = false;

    public function __construct(Ghost $ghost, ReqContainer $container, InputMsg $input)
    {
        $this->ghost = $ghost;
        $this->ghostConfig = $ghost->getConfig();
        $this->input = $input;
        // id
        $this->clonerId = 'ght:' . $ghost->getId() .':'. $input->getSessionId();

        // expire
        $this->expire = $this->ghostConfig->sessionExpire;
        parent::__construct($container, $input->getConversationId());
    }


    public function getId(): string
    {
        return $this->clonerId;
    }

    /*-------- conversation id ---------*/

    public function getConversationId() : string
    {
        if (isset($this->conversationId)) {
            return $this->conversationId;
        }

        if ($this->isStateless()) {
            $convoId = $this->input->getConversationId();
            $convoId = empty($convoId)
                ? $this->makeConvoId()
                : $convoId;

            return $this->conversationId = $convoId;
        }

        $inputSid = $this->input->getConversationId();
        $cachedSid = $this->getConvoIdFromCache();

        if (empty($cachedSid)) {
            $convoId = $inputSid ?? $this->makeConvoId();

        } elseif(empty($inputSid)) {
            $convoId = $cachedSid;
        } else {
            $convoId = $inputSid;
        }

        return $this->conversationId = $convoId;
    }

    protected function makeConvoId() : string
    {
        $clonerId = $this->getId();
        $messageId = $this->input->getMessageId();
        return sha1("cloner:$clonerId:message:$messageId");
    }

    protected function getConvoIdFromCache() : ? string
    {
        $key = $this->getConvoCacheKey();
        return $this->cache->get($key);
    }

    protected function cacheConvoId(string $convoId) : void
    {
        $key = $this->getConvoCacheKey();
        $this->cache->set($key, $convoId, $this->getSessionExpire());
    }

    protected function ioDeleteConvoIdCache() : void
    {
        $key = $this->getConvoCacheKey();
        $this->cache->forget($key);
    }

    protected function getConvoCacheKey() : string
    {
        $cloneId = $this->getId();
        return "clone:$cloneId:convo:id";
    }

    /*-------- properties ---------*/

    protected function getProtocalOptions(): array
    {
        return $this->ghostConfig->protocals;
    }


    public function getStorage(): SessionStorage
    {
        return $this->storage;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /*------- getter -------*/

    public function __get($name)
    {
        if ($name === 'ghost') {
            return $this->ghost;
        }

        if ($name === 'ghostConfig') {
            return $this->ghostConfig;
        }

        if ($name === 'input') {
            return $this->input;
        }

        return parent::__get($name);
    }

    /*------- cache -------*/

    protected function getGhostClonerLockerKey() : string
    {
        $ghostId = $this->ghostConfig->id;
        $clonerId = $this->getId();
        return "ghost:$ghostId:clone:$clonerId:locker";
    }

    public function lock(int $second): bool
    {
        $ttl = $this->ghostConfig->sessionLockerExpire;
        if ($ttl > 0) {
            $locker = $this->getGhostClonerLockerKey();
            return $this->cache->lock($locker, $ttl);
        } else {
            return true;
        }

    }

    public function isLocked(): bool
    {
        $ttl = $this->ghostConfig->sessionLockerExpire;
        if ($ttl > 0) {
            $locker = $this->getGhostClonerLockerKey();
            return $this->cache->has($locker);
        }
        return false;
    }

    public function unlock(): bool
    {
        $ttl = $this->ghostConfig->sessionLockerExpire;
        if ($ttl > 0) {
            $locker = $this->getGhostClonerLockerKey();
            return $this->cache->unlock($locker);
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

    public function silence(bool $silent = true): void
    {
        $this->silent = $silent;
    }

    public function output(IntercomMsg $output, IntercomMsg ...$outputs): void
    {
        array_unshift($outputs, $output);
        if (!$this->silent) {
            $this->outputs = array_reduce(
                $outputs,
                function($outputs, IntercomMsg $output){
                    $outputs[] = $output;
                    return $outputs;
                },
                $this->outputs
            );
        }
    }

    public function getOutputs(): array
    {
        return $this->outputs;
    }

    public function asyncInput(InputMsg $input): void
    {
        if (!$this->silent) {
            $this->asyncInputs[] = $input;
        }
    }

    public function getAsyncInput(): array
    {
        return $this->asyncInputs;
    }


    /*------- flush -------*/

    protected function flushInstances(): void
    {
        $this->ghost = null;
        $this->ghostConfig = null;
        $this->input = null;
        $this->contexts = [];
        $this->outputs = [];
        $this->asyncInputs = [];
    }

    protected function quitSession(): void
    {
        $this->output($this->input->output(new SessionQuitInt()));
        $this->ioDeleteConvoIdCache();
    }

    protected function saveSession(): void
    {
        // runtime 更新.
        if (!$this->isSingletonInstanced('runtime')) {
            $this->runtime->save();
        }

        // storage 更新.
        if ($this->isSingletonInstanced('storage')) {
            $this->storage->save();
        }

        // 更新 sessionId 缓存.
        if (isset($this->conversationId)) {
            $this->cacheConvoId($this->conversationId);
        }
    }

}