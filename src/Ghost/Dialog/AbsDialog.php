<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Await;
use Commune\Blueprint\Ghost\Memory\Recollection;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Tools;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Runtime\Operators\AbsOperator;
use Commune\Ghost\ITools;
use Commune\Support\DI\Injectable;
use Commune\Support\DI\TInjectable;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Exceptions\Runtime\BrokenRequestException;
use Commune\Support\Utils\TypeUtils;


/**
 * 抽象的 Dialog 实现.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Cloner $cloner
 * @property-read Context $context
 * @property-read Ucl $ucl              当前 Dialog 的 Context 地址.
 * @property-read Dialog|null $prev     前一个 Dialog
 */
abstract class AbsDialog extends AbsOperator implements
    Dialog,
    Injectable,
    Tools\Caller,
    Operator
{
    use TInjectable;

    /*------ params -------*/

    /**
     * @var Cloner
     */
    protected $_cloner;

    /**
     * @var Ucl
     */
    protected $_ucl;

    /**
     * @var AbsDialog|null
     */
    protected $_prev;


    /*------ cached -------*/

    /**
     * @var Process|null
     */
    protected $process;

    /**
     * @var callable[]
     */
    protected $stack = [];

    /**
     * AbsBaseDialog constructor.
     * @param Cloner $cloner
     * @param Ucl $ucl
     * @param AbsDialog|null $prev
     */
    public function __construct(Cloner $cloner, Ucl $ucl, AbsDialog $prev = null)
    {
        $this->_cloner = $cloner;
        $this->_ucl = $ucl->toInstance($cloner);
        $this->_prev = $prev;
    }

    /**
     * 寻找到下一个 Intend 的对象.
     * @return static
     */
    abstract protected function runTillNext() : Operator;

    protected function runIntercept(): Operator
    {
        if ($this instanceof Dialog\Intend) {
            $stageDef = $this->_ucl->findStageDef($this->_cloner);
            return $stageDef->onIntend($this->prev, $this);
        }

        return null;
    }

    protected function toNext(): Operator
    {
        // 允许放弃当前节点的执行.
        $next = $this->runIntercept();
        if (isset($next)) {
            return $next;
        }

        $this->runStack();

        return $this->runTillNext();
    }

    /**
     * @return Process
     */
    protected function getProcess() : Process
    {
        return $this->process
            ?? $this->process = $this->_cloner->runtime->getCurrentProcess();
    }

    protected function setProcess(Process $process) : void
    {
        $this->process = $process;
        $this->_cloner->runtime->setCurrentProcess($process);
    }


    /*-------- implements --------*/

    public function send(): Tools\Deliver
    {
        return new ITools\IDeliver($this);
    }

    public function redirect(): Tools\Navigator
    {
        return new ITools\IRedirect($this);
    }

    public function await(
        array $allowContexts = [],
        array $stageRoutes = [],
        int $expire = null
    ): Await
    {
        return $this->redirect()->await($allowContexts, $stageRoutes, $expire);
    }


    public function hearing(): Tools\Receive
    {
        return new ITools\IHearing($this);
    }

    /*-------- history --------*/

    protected function depth(): int
    {
        $current = $this;
        $depth = 1;

        while(isset($current)) {
            $current = $current->prev;
            $depth++;
        }

        return $depth;
    }


    /*-------- caller --------*/

    public function caller(): Tools\Caller
    {
        return $this;
    }

    public function call($caller, array $parameters = [])
    {
        if (
            is_string($caller)
            && class_exists($caller)
            && method_exists($caller, '__invoke')
        ) {
            $caller = [$caller, '__invoke'];
        }

        $parameters = $this->getContextualInjections($parameters);

        try {
            return $this->_cloner->container->call($caller, $parameters);
        } catch (\Exception $e) {
            throw new BrokenRequestException('', $e);
        }
    }

    public function predict(callable $caller): bool
    {
        $result = $this->call($caller);
        if (!is_bool($result)) {
            throw new InvalidArgumentException('caller is not predict which return with bool');
        }

        return $result;
    }

    public function operate(callable $caller): ? Operator
    {
        $result = $this->call($caller);
        if (is_null($result) || $result instanceof Operator) {
            return $result;
        }

        throw new InvalidArgumentException('caller should return operator or null, ' . TypeUtils::getType($result) . ' given');
    }

    protected function getContextualInjections(array $parameters) : array
    {
        $injections = [
            'context' => $this->context,
            'prev' => $this->_prev,
            'dialog' => $this,
        ];

        $injections = $parameters + $injections;

        foreach ($injections as $key => $value) {

            $parameters[$key] = $value;

            if (!$value instanceof Injectable) {
                continue;
            }

            foreach ($value->getInterfaces() as $interface) {
                $parameters[$interface] = $value;
            }
        }

        return $parameters;
    }


    /*-------- operator --------*/

    public function dumpStack() : array
    {
        $stack = $this->stack;
        $this->stack = [];
        return $stack;
    }

    public function pushStack(callable $caller) : void
    {
        $this->stack[] = $caller;
    }

    protected function runStack() : void
    {
        $prev = $this->_prev;

        $stack = $this->dumpStack();
        if (isset($prev)) {
            $stack = array_merge($prev->dumpStack(), $stack);
        }

        while($caller = array_shift($stack)) {
            $caller($this);
        }
    }

    public function getOperatorDesc(): string
    {
        $name = static::class;
        $ucl = $this->_ucl->toEncodedStr();
        return "$name : $ucl";
    }

    /*-------- status --------*/

    public function isEvent(string $statusType): bool
    {
        return is_a($this, $statusType, TRUE);
    }


    public function recall(string $name): Recollection
    {
        return $this
            ->cloner
            ->mind
            ->memoryReg()
            ->getDef($name)
            ->recall($this->_cloner);
    }

    protected function runAwait(bool $silent = false ) : void
    {
        $process = $this->getProcess();
        $waiter = $process->waiter;

        if (!isset($waiter) || $silent) {
            return;
        }

        // 如果是 waiter, 重新输出 question
        $question = $waiter->question;
        $input = $this->_cloner->input;
        if (isset($question)) {
            $this->_cloner->output($input->output($question));
        }

        // 尝试同步状态变更.
        $contextMsg = $this->_cloner->runtime->toContextMsg();
        if (isset($contextMsg)) {
            $this->_cloner->output($input->output($contextMsg));
        }
    }


    /*-------- getter --------*/

    public function __isset($name)
    {
        if ($name === 'prev') {
            return isset($this->_prev);
        }

        return in_array($name, ['cloner', 'ucl', 'context', 'depth']);
    }

    public function __get($name)
    {
        switch ($name) {
            case 'depth' :
                return $this->depth();
            case 'cloner' :
                return $this->_cloner;
            case 'ucl' :
                return $this->_ucl;
            case 'prev' :
                return $this->_prev;
            case 'context' :
                return $this->_ucl->findContext($this->_cloner);
            default:
                return null;
        }

    }

    public function __destruct()
    {
        $prev = $this->_prev;
        unset($this->_prev);

        if ($prev instanceof self) {
            $prev->__destruct();
        }

        $this->_cloner = null;
        $this->_ucl = null;
        $this->process = null;
        $this->stack = [];
    }

    public function getInterfaces(): array
    {
        return static::getInterfacesOf(Dialog::class, false);
    }

    public function __invoke(): Dialog
    {
        return $this;
    }


}