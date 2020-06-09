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

use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Cloner\ClonerInstanceStub;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Memory\Recollection;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\Runtime\Task;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Framework\Spy\SpyAgency;
use Commune\Message\Host\Convo\IContextMsg;
use Commune\Protocals\HostMsg\Convo\ContextMsg;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Arr\TArrayAccessToMutator;
use Commune\Blueprint\Exceptions\CommuneLogicException;
use Commune\Support\DI\TInjectable;

/**
 * 上下文语境的默认.
 * 持有 Context 的上下文记忆 Memory, 用于读/写真正的数据.
 *
 *
 * 通常不是 New 出来, 而是 Cloner::findContext() 或者 Cloner::newContext() 来获取
 * @see Cloner
 *
 * 最终通过 ContextDef::wrapContext() 完成包装.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IContext implements Context
{
    use TInjectable, ArrayAbleToJson, TArrayAccessToMutator;

    /**
     * @var Ucl
     */
    protected $_ucl;

    /**
     * @var Cloner
     */
    protected $_cloner;

    /**
     * @var Recollection|null
     */
    protected $_recollection;

    /**
     * @var ContextDef|null
     */
    protected $_def;

    /**
     * @var Task|null
     */
    protected $_task;

    /**
     * IContext constructor.
     * @param Ucl $ucl
     * @param Cloner $cloner
     */
    public function __construct(
        Cloner $cloner,
        Ucl $ucl
    )
    {
        $this->_cloner = $cloner;
        $this->_ucl = $ucl;
        SpyAgency::incr(static::class);
    }

    public static function create(Cloner $cloner, Ucl $ucl): Context
    {
        return new static($cloner, $ucl);
    }


    public function toInstanceStub(): ClonerInstanceStub
    {
        return new ContextStub($this->_ucl->encode());
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

    public function getQuery(): array
    {
        return $this->_ucl->query;
    }

    public function getCloner(): Cloner
    {
        return $this->_cloner;
    }

    public function getTask(): Task
    {
        return $this->_task
            ?? $this->_task = $this->_cloner
                ->runtime
                ->getCurrentProcess()
                ->getTask($this->_ucl);
    }


    public function getUcl(): Ucl
    {
        return $this->getTask()->getUcl();
    }

    public function getStage(string $stage = ''): Ucl
    {
        return $this->getUcl()->goStage($stage);
    }

    public function getStages(array $stages): array
    {
        return array_map(
            function ($stage) {
                return $stage instanceof Ucl
                    ? $stage
                    : $this->_ucl->goStage($stage);
            },
            $stages
        );
    }


    /*----- entities -----*/

    public function depending(): ? string /* entityName */
    {
        $depending = $this
            ->getDef()
            ->getDependingAttrs();


        foreach ($depending as $name) {
            if (!$this->offsetExists($name)) {
                return $name;
            }
        }

        return null;
    }

    public function isPrepared(): bool
    {
        $depending = $this->depending();
        return is_null($depending);
    }

    public function isChanged(): bool
    {
        if (isset($this->_recollection)) {
            return $this->_recollection->isChanged();
        }

        return false;
    }


    /*----- memory -----*/

    protected function getRecollection() : Recollection
    {
        return $this->_recollection
            ?? $this->_recollection = $this
                ->getDef()
                ->asMemoryDef()
                ->recall($this->_cloner, $this->_ucl->getContextId());

    }

    /*----- ArrayAccess -----*/

    public function toArray(): array
    {
        $data = $this->getQuery();
        $data = $data + $this->getRecollection()->toArray();

        return $data;
    }

    public function toData(): array
    {
        return $this->getRecollection()->toData();
    }

    public function merge(array $data): void
    {
        if (empty($data)) {
            return;
        }

        foreach ($data as $key => $val) {
            $this->offsetSet($key, $val);
        }
    }


    public function toContextMsg(): ContextMsg
    {
        return new IContextMsg([
            'contextName' => $this->_ucl->contextName,
            'contextId' => $this->_ucl->getContextId(),
            'stageName' => $this->_ucl->stageName,
            'query' => $this->_ucl->query,
            'data' => $this->toData(),
        ]);
    }

    public function getIterator()
    {
        $queries = $this->getQuery();
        foreach ($queries as $value) {
            yield $value;
        }

        foreach ($this->getRecollection() as $value) {
            yield $value;
        }
    }


    /*----- ArrayAccess -----*/

    public function offsetExists($offset)
    {

        $value = $this->offsetGet($offset);
        return isset($value);
    }

    public function offsetGet($offset)
    {
        $query = $this->getQuery();

        if(array_key_exists($offset, $query)) {
            return $query[$offset];
        }

        $value = $this->getRecollection()->offsetGet($offset);
        return $value instanceof Context && !$value->isPrepared()
            ? null
            : $value;
    }

    public function offsetSet($offset, $value)
    {
        $this->getRecollection()->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $query = $this->getQuery();

        if (array_key_exists($offset, $query)) {
            $contextName = $this->getName();
            $error = "context $contextName try to unset value for query parameter $offset";
            $this->warningOrException($error);
            return;
        }

        $this->getRecollection()->offsetUnset($offset);
        return;
    }

    protected function warningOrException(string $error)
    {
        if (CommuneEnv::isDebug()) {
            $this->_cloner->logger->warning($error);
        } else {
            throw new CommuneLogicException($error);
        }
    }

    /*----- injectable -----*/

    public function getInterfaces(): array
    {
        return static::getInterfacesOf(Context::class);
    }

    public function __destruct()
    {
        unset($this->_def);
        unset($this->_ucl);
        unset($this->_cloner);
        unset($this->_task);
        unset($this->_recollection);
        SpyAgency::decr(static::class);
    }
}