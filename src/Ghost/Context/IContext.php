<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context;

use Commune\Blueprint\Exceptions\HostLogicException;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Cloner\ClonerInstanceStub;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindDef\DefParam;
use Commune\Blueprint\Ghost\Memory\Memory;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Message\Host\Convo\IContextMsg;
use Commune\Protocals\Host\Convo\ContextMsg;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\DI\TInjectable;
use Illuminate\Support\Collection;

/**
 * 上下文语境的默认.
 * 持有 Context 的上下文记忆 Memory, 用于读/写真正的数据.
 *
 *
 * 通常不是 New 出来, 而是 Cloner::findContext() 或者 Cloner::newContext() 来获取
 * @see Cloner
 *
 * 最终通过 ContextDef::wrapContext() 完成包装.
 * @see ContextDef
 *
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IContext implements Context
{
    use TInjectable,  ArrayAbleToJson;

    protected $_getter_prefix = '__get_';
    protected $_setter_prefix = '__set_';

    /**
     * @var Ucl
     */
    protected $_ucl;

    /**
     * @var Cloner
     */
    protected $_cloner;

    /**
     * @var Memory|null
     */
    protected $_memory;

    /**
     * @var ContextDef|null
     */
    protected $_def;

    /**
     * @var Collection|null
     */
    protected $_query;


    public function __construct(
        Ucl $ucl,
        Cloner $cloner
    )
    {
        $this->_ucl = $ucl->gotoStage('');
        $this->_cloner = $cloner;
    }

    public function toInstanceStub(): ClonerInstanceStub
    {
        return new ContextStub($this->_ucl->toEncodedUcl());
    }


    /*----- property -----*/

    public function getDef(): ContextDef
    {
        return $this->_def
            ?? $this->_def = $this->_ucl->findContextDef($this->_cloner);
    }

    public function getId(): string
    {
        return $this->_ucl->getContextId();
    }

    public function getName(): string
    {
        return $this->_ucl->contextName;
    }

    public function getPriority(): int
    {
        return $this->getDef()->getPriority();
    }

    public function getQuery(): Collection
    {
        return $this->_query
            ?? $this->_query = new Collection($this->_ucl->query);
    }


    /*----- entities -----*/

    public function dependEntity(): ? string /* entityName */
    {
        $manager = $this->getDef()->getParamsManager();
        $entities = $manager->getEntityParams();

        foreach ($entities as $name) {
            if (!$this->offsetExists($name)) {
                return $name;
            }
        }

        return null;
    }


    /*----- memory -----*/

    protected function getLongTermMemory() : Memory
    {
        if (isset($this->_longTermMemory)) {
            return $this->_longTermMemory;
        }

        $manager = $this->getDef()->getParamsManager();
        $parameters = $manager->getLongTermParams();
        return $this->_longTermMemory = $this->findMemory($parameters);

    }

    protected function getSessionMemory() : Memory
    {
        if (isset($this->_memory)) {
            return $this->_memory;
        }

        $manager = $this->getDef()->getParamsManager();
        $parameters = $manager->getShortTermParams();
        return $this->_memory = $this->findMemory($parameters);

    }

    protected function findMemory(Collection $parameters) : Memory
    {
        $stub = array_map(function(DefParam $parameter){
            return $parameter->getDefault();
        }, $parameters->all());

        return $this->_cloner
            ->runtime
            ->findMemory($this->getId(), true, $stub);
    }

    /*----- ArrayAccess -----*/

    public function toArray(): array
    {
        $data = $this->getQuery()->toArray();

        $manager = $this->getDef()->getParamsManager();

        if ($manager->hasLongTermParameter()) {
            $data = $data + $this->getLongTermMemory()->toArray();
        }

        if ($manager->hasSessionParameter()) {
            $data = $data + $this->getSessionMemory()->toArray();
        }

        return $data;
    }

    public function toMemorableData(): array
    {
        $data = [];

        $manager = $this->getDef()->getParamsManager();

        if ($manager->hasLongTermParameter()) {
            $data = $data + $this->getLongTermMemory()->toData();
        }

        if ($manager->hasSessionParameter()) {
            $data = $data + $this->getSessionMemory()->toData();
        }

        // 不包含任何 object 对象.
        return array_filter($data, function($value) {
            return !is_object($value);
        });
    }

    public function merge(array $data): void
    {
        foreach ($data as $key => $val) {
            $this->offsetSet($key, $val);
        }
    }


    public function toContextMsg(): ContextMsg
    {
        return new IContextMsg([
            'contextName' => $this->_ucl->contextName,
            'contextId' => $this->_ucl->getContextId(),
            'query' => $this->_ucl->query,
            'data' => $this->toMemorableData(),
        ]);
    }

    public function getIterator()
    {
        $manager = $this->getDef()->getParamsManager();
        foreach ($manager->getParameters() as $name => $parameter) {
            yield $this->offsetGet($name);
        }
    }


    /*----- ArrayAccess -----*/

    public function offsetExists($offset)
    {
        $manager = $this->getDef()->getParamsManager();

        if (!$manager->hasParameter($offset)) {
            return false;
        }

        $parameter = $manager->getParameter($offset);

        if ($parameter->isQuery()) {
            return $this->getQuery()->offsetExists($offset);
        }

        if ($parameter->isLongTerm()) {
            return $this->getLongTermMemory()->offsetExists($offset);
        }

        return $this->getSessionMemory()->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        // getter
        $method = $this->_getter_prefix . $offset;
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        $manager = $this->getDef()->getParamsManager();
        if (!$manager->hasParameter($offset)) {
            return null;
        }

        $parameter = $manager->getParameter($offset);

        if ($parameter->isLongTerm()) {
            $value = $this->getLongTermMemory()->offsetGet($offset);
        } else {
            $value = $this->getSessionMemory()->offsetGet($offset);
        }

        if ($value instanceof Cloner\ClonerInstanceStub) {
            $value = $value->toInstance($this->_cloner);
        }

        return $value ?? $parameter->getDefault();
    }

    public function offsetSet($offset, $value)
    {
        // setter
        $method = $this->_setter_prefix . $offset;
        if (method_exists($this, $method)) {
            $this->{$method}($value);
            return;
        }

        // set undefined
        $manager = $this->getDef()->getParamsManager();
        if (!$manager->hasParameter($offset)) {
            $contextName = $this->getName();
            $error = "context $contextName try to set value for undefined parameter $offset";
            $this->warningOrException($error);
            return;
        }

        $parameter = $manager->getParameter($offset);
        if ($parameter->isQuery()) {
            $contextName = $this->getName();
            $error = "context $contextName try to set value for query parameter $offset";
            $this->warningOrException($error);
        }

        if ($value instanceof Cloner\ClonerInstance) {
            $value = $value->toInstanceStub();
        }


        // 进行 value 的过滤. 主要是数组和类型的切换.
        $value = $parameter->parseSetVal($value);
        if ($parameter->isLongTerm()) {
            $this->getLongTermMemory()->offsetSet($offset, $value);
            return;
        }

        $this->getSessionMemory()->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $manager = $this->getDef()->getParamsManager();
        if (!$manager->hasParameter($offset)) {
            return;
        }

        $parameter = $manager->getParameter($offset);

        if ($parameter->isLongTerm()) {
            $this->getLongTermMemory()->offsetUnset($offset);
            return;
        }

        $this->getSessionMemory()->offsetUnset($offset);
    }



    protected function warningOrException(string $error)
    {
        if ($this->_cloner->isDebugging()) {
            $this->_cloner->logger->warning($error);
        } else {
            throw new HostLogicException($error);
        }
    }

    /*----- injectable -----*/

    public function getInterfaces(): array
    {
        return static::getInterfacesOf(Context::class);
    }


    public function __destruct()
    {
        $this->_def = null;
        $this->_query = null;
        $this->_ucl = null;
        $this->_cloner = null;
    }
}