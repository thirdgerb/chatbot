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

use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Configs\GhostConfig;
use Commune\Blueprint\Exceptions\CommuneRuntimeException;
use Commune\Blueprint\Exceptions\Runtime\BrokenRequestException;
use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Framework\Auth\Authority;
use Commune\Blueprint\Ghost;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Contracts\Cache;
use Commune\Contracts\Trans\SelfTranslatable;
use Commune\Contracts\Trans\Translator;
use Commune\Framework\ASession;
use Commune\Ghost\IOperate\OStart;
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
        'dispatcher' => Cloner\ClonerDispatcher::class,

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
    protected $noConvoState = false;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var bool
     */
    protected $silent = false;

    /**
     * 判断当前 Cloner 的进程是否是子进程.
     * @var bool
     */
    protected $isSubProcess = false;

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

        // 如果是无状态的, 随便生成一个 conversationId.
        if ($this->isStateless()) {
            $convoId = $this->inputConvoId;
            $convoId = empty($convoId)
                ? $this->makeConvoId()
                : $convoId;

            return $this->conversationId = $convoId;
        }

        // 当输入消息指定 conversationId
        // 如果 session 还没有任何 conversation, 则创建一个.
        // 相反, 如果 session 已经有了 conversation, 这相当于为当前 Session 创建了一个子进程.
        $inputCid = $this->inputConvoId;
        $cachedCid = $this->getConvoIdFromCache();

        // 如果没有缓存, 重新生成一个
        if (empty($cachedCid)) {
            $convoId = empty($inputCid) ? $this->makeConvoId() : $inputCid;
            // 将新建的 conversation id 保存当主进程里.
            $this->cacheConvoId($convoId);

        // 输入没有带 conversation id, 认为访问的是主进程.
        } elseif(empty($inputCid)) {
            $convoId = $cachedCid;

        // 输入携带了 conversation id, 则认为访问的是子进程.
        } else {
            $convoId = $inputCid;
            $this->isSubProcess = true;
        }

        return $this->conversationId = $convoId;
    }

    public function isClonerExists(string $sessionId): bool
    {
        $key = $this->getConvoCacheKey($sessionId);
        return $this->__get('cache')->has($key);
    }

    protected function makeConvoId() : string
    {
        $clonerId = $this->getSessionId();
        $traceId = $this->_container->getId();
        return sha1("cloner:$clonerId:req:$traceId");
    }

    /**
     * 从缓存中获取当前 session 的 主conversation id
     * @return null|string
     */
    protected function getConvoIdFromCache() : ? string
    {
        $key = $this->getConvoCacheKey();
        return $this->__get('cache')->get($key);
    }

    /**
     * 缓存当前 Session 的主 conversation.
     * @param string $convoId
     */
    protected function cacheConvoId(string $convoId) : void
    {
        $key = $this->getConvoCacheKey();
        $this->__get('cache')->set($key, $convoId, $this->getSessionExpire());
    }

    /**
     * 删除 session 的 conversationId cache.
     */
    protected function ioDeleteConvoIdCache() : void
    {
        $key = $this->getConvoCacheKey();
        $this->__get('cache')->forget($key);
    }

    protected function getConvoCacheKey(string $sessionId = null) : string
    {
        $cloneId = $sessionId ?? $this->getSessionId();
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

    /**
     * 生成 Cloner 的锁 key
     * @return string
     */
    protected function getGhostClonerLockerKey() : string
    {
        $ghostId = $this->getAppId();
        $clonerId = $this->getSessionId();
        $convoId = $this->getConversationId();

        // 之所以使用这么长的 key 名, 是为了可以在目标中追踪.
        // 当然会导致读写的开销.
        return "ghost:$ghostId:clone:$clonerId:convo:$convoId:locker";
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

    /*------- dialog manager -------*/

    public function runDialogManager(Ghost\Operate\Operator $start = null): void
    {
        $operator = $start ?? new OStart($this);
        $tracer = $this->runtime->trace;

        try {

            while (isset($operator)) {

                $tracer->record($operator);
                $operator = $operator->tick();
                if ($operator->isTicked()) {
                    break;
                }
            }

        } catch (CommuneRuntimeException $e) {
            throw $e;

        } catch (\Throwable $e) {
            throw new BrokenRequestException($e->getMessage(), $e);

        } finally {
            // 调试模式下检查运行轨迹.
            if (CommuneEnv::isDebug()) {
                $tracer->log($this->logger);
            }
        }
    }


    /*------- output -------*/

    public function silence(bool $silent = true): void
    {
        $this->silent = $silent;
    }


    public function output(OutputMsg $output, OutputMsg ...$outputs): void
    {
        // 如果设置了静默, 则不接受任何消息. 当然也包括 intent 相关的控制消息.
        if ($this->silent) {
            return;
        }

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
        $this->noConvoState = true;
    }

    public function isConversationEnd(): bool
    {
        return $this->quit;
    }

    public function noConversationState(): void
    {
        $this->noConvoState = true;
    }

    public function isSubProcess() : bool
    {
        return $this->isSubProcess;
    }

    /*------- flush -------*/

    /**
     * 这个方法是 CommuneChatbot 项目精华中的精华之一
     * 通过反复的实践和试错才成型, 当然仍然需要不断完善.
     *
     * 这个方法实现了对话机器人的 多进程, 从而实现了基于多进程的异步任务.
     *
     * 只需要投递一个携带了 conversationId 的异步 input, 就可以开启一个子进程.
     * 而这个子进程又能够共享当前的 session, 对用户进行广播.
     *
     * 关键在于, 有两种维度
     *
     * - sessionId (cloner)
     * - conversationId
     *
     * 两种状态:
     *
     * - session stateless
     * - conversation no state
     *
     * 每一轮对话最后根据不同情况:
     *
     * - isSubProcess
     * - isConversationEnd
     *
     * 保存:
     *
     * - convoId
     * - runtime
     *
     * 真是太牛了 (希望不要打脸)
     *
     * @return string[]
     */
    protected function saveSession(): array
    {
        $steps = [];
        // 无状态的请求不做任何缓存.
        if ($this->isStateless()) {
            $steps[] = 'stateless';
            return $steps;
        }


        // 如果当前不是主进程, 会话又结束时, 删除主进程的 conversationId 缓存.
        if ($this->isConversationEnd()) {

            // 同时也删除 storage 会话.
            $this->setSessionExpire(0);
            $steps[] = 'set session expire 0';

            // 会话的主进程, 删除 convoId 的缓存
            if (!$this->isSubProcess) {
                $this->ioDeleteConvoIdCache();
                $steps[] = 'del convo id cache';
            }

        //  给当前 conversation id 续命.
        } elseif (
            !$this->isSubProcess
            && !$this->noConvoState
            && isset($this->conversationId)
        ) {
            $this->cacheConvoId($this->conversationId);
            $steps[] = 'cache convo id';
        }

        // storage 更新.
        // 所以 Storage 是跟随 Session, 而不是跟随 Conversation
        // 因此 Storage 可以跨越多个 Conversation 存在.
        if ($this->isSingletonInstanced('storage')) {
            $this->__get('storage')->save();
            $steps[] = 'save storage';
        }

        // 保存当前 conversation 的 runtime.
        if ($this->isSingletonInstanced('runtime')) {
            /**
             * @var Ghost\Runtime\Runtime $runtime
             */
            $runtime = $this->__get('runtime');

            // 会话结束, 删除会话缓存
            if ($this->isConversationEnd()) {
                $runtime->flush();
                $steps[] = 'flush runtime';

            // 允许保存会话状态.
            } elseif (!$this->noConvoState) {
                $runtime->save();
                $steps[] = 'save runtime';
            }
        }

        return $steps;
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