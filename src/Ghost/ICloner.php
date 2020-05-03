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

use Commune\Blueprint\Ghost\GhostConfig;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Framework\Session\Storage;
use Commune\Blueprint\Ghost;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Contracts\Cache;
use Commune\Framework\ASession;
use Commune\Ghost\OperatorsBack\DialogManager;
use Commune\Protocals\Comprehension;
use Commune\Protocals\Intercom\GhostInput;
use Commune\Support\Option\OptRegistry;
use Psr\Log\LoggerInterface;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ICloner extends ASession implements Cloner
{

    const SINGLETONS =  [
        'scope' => Ghost\ClonerScope::class,
        'config' => GhostConfig::class,
        'convo' => Ghost\Convo::class,
        'cache' => Cache::class,
        'auth' => Ghost\Auth\Authority::class,
        'mind' => Ghost\Mind\Mindset::class,
        'runtime' => Ghost\Runtime\Runtime::class,
        'registry' => OptRegistry::class,
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
     * @var GhostInput
     */
    protected $ghostInput;

    /*------- cached -------*/

    /**
     * @var string
     */
    protected $clonerId;

    /**
     * @var string
     */
    protected $hostName;

    /**
     * @var int
     */
    protected $expire;

    public function __construct(Ghost $ghost, ReqContainer $container, GhostInput $input)
    {
        $this->ghost = $ghost;
        $this->ghostConfig = $ghost->getConfig();
        $this->ghostInput = $input;

        // id
        $this->clonerId = $input->getCloneId();
        $this->hostName = $input->hostName;

        // expire
        $this->expire = $this->ghostConfig->sessionExpire;

        parent::__construct($container, $input->sessionId);
    }


    public function getClonerId(): string
    {
        return $this->clonerId;
    }


    public function runDialogManager(Operator $operator = null): bool
    {
        $manager = new DialogManager($this);
        return $manager->runDialogManage($operator);
    }

    public function getContext(string $contextId, string $contextName): Context
    {
        $contextDef = $this->mind->contextReg()->getDef($contextName);
        $recollection = $this->runtime->findRecollection($contextId);

        if (isset($recollection)) {
            return $contextDef->wrapContext($recollection, $this);
        }

        return $this->newContext($contextName);
    }

    public function newContext(string $contextName, array $queries = null): Context
    {
        $queries = $queries ?? $this
                ->ghostInput
                ->comprehension
                ->intention
                ->getIntentEntities($contextName);

        // Def
        $contextDef = $this->mind->contextReg()->getDef($contextName);

        // 生成新的 Id
        $id = $contextDef->makeId($this);

        // 获得 Recollection 的默认值.
        $values = $contextDef->getDefaultValues();
        $queries = empty($queries)
            ? []
            : $contextDef->parseIntentEntities($queries);
        $values = $queries + $values;

        // 创建新的记忆体.
        $recollection = $this->runtime->createRecollection(
            $id,
            $contextDef->getName(),
            $contextDef->isLongTerm(),
            $values
        );

        // 生成 Context 对象
        return $contextDef->wrapContext($recollection, $this);
    }


    protected function getProtocalOptions(): array
    {
        return $this->ghostConfig->protocals;
    }

    public function getName(): string
    {
        return $this->config->name;
    }

    public function getStorage(): Storage
    {
        return $this->storage;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }


    public function getSessionId(): string
    {
        return $this->sessionId;
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

        if ($name === 'ghostInput') {
            return $this->ghostInput;
        }

        return parent::__get($name);
    }

    /*------- cache -------*/

    protected function getGhostClonerLockerKey() : string
    {
        $ghostId = $this->ghostConfig->id;
        $clonerId = $this->getClonerId();
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


    protected function flushInstances(): void
    {
        $this->ghost = null;
        $this->ghostConfig = null;
        $this->ghostInput = null;
    }

    protected function saveSession(): void
    {
        // runtime 更新.
        $this->runtime->save();
        // storage 更新.
        $this->storage->save();
    }

}