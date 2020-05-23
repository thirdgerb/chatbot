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
use Commune\Blueprint\Ghost\Dialog\Finale\Await;
use Commune\Blueprint\Ghost\Memory\Recollection;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Tools\Hearing;
use Commune\Blueprint\Ghost\Tools\Matcher;
use Commune\Blueprint\Ghost\Tools\Navigator;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Tools\Caller;
use Commune\Blueprint\Ghost\Tools\Deliver;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Tools\IDeliver;
use Commune\Ghost\Tools\IHearing;
use Commune\Ghost\Tools\INavigator;
use Commune\Support\DI\Injectable;
use Commune\Support\DI\TInjectable;
use Commune\Blueprint\Exceptions\HostLogicException;
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
abstract class AbsDialog implements
    Dialog,
    Injectable,
    Caller,
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
     * @var bool
     */
    protected $ticked = false;

    /**
     * @var bool
     */
    protected $ticking = false;

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
        $this->stack = isset($prev) ? $prev->dumpStack() : [];
    }


    /**
     * 寻找到下一个 Intend 的对象.
     * @return static
     */
    abstract protected function runTillNext() : Operator;

    /**
     * @return Process
     */
    protected function getProcess() : Process
    {
        return $this->process
            ?? $this->process = $this->_cloner->runtime->getCurrentProcess();
    }


    /*-------- implements --------*/

    public function send(): Deliver
    {
        return new IDeliver($this);
    }

    public function matcher(): Matcher
    {
        return $this->_cloner->matcher->refresh();
    }

    public function nav(): Navigator
    {
        return new INavigator($this);
    }

    public function await(
        array $allowContexts = [],
        array $stageRoutes = [],
        int $expire = null
    ): Await
    {
        return $this->nav()->await($allowContexts, $stageRoutes, $expire);
    }


    public function hearing(): Hearing
    {
        return new IHearing($this);
    }

    /*-------- history --------*/

    public function depth(): int
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

    public function caller(): Caller
    {
        return $this;
    }

    public function make(string $abstract, array $parameters = [])
    {
        $parameters = $this->getContextualInjections($parameters);
        return $this->_cloner->container->make($abstract, $parameters);
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

    public function ticked(): void
    {
        $this->ticked = true;
    }


    public function tick() : Operator
    {
        // 每个 Dialog 实例只能 tick 一次.
        if ($this->ticked) {
            throw new HostLogicException(
                __METHOD__
                . ' try to tick dialog that ticked'
            );
        }

        if ($this->ticking) {
            throw new HostLogicException(
                __METHOD__
                . ' try to tick dialog that ticking'
            );
        }

        $this->ticking = true;

        // 尝试拦截.
        if ($this instanceof Dialog\Intercept && isset($this->_prev)) {
            $stageDef = $this->_ucl->findStageDef($this->_cloner);
            $next = $stageDef->onIntercept($this->_prev, $this);
        }

        // 未被拦截的时候.
        if (!isset($next)) {
            // 下一次 tick 关闭上一次tick
            $prev = $this->_prev;
            if (isset($prev)) {
                $prev->ticked();
            }

            $this->runStack();

            $next = $this->runTillNext();
        }

        $this->ticking = false;
        return $next;
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

    public function isTicking(): bool
    {
        return $this->ticking;
    }

    public function isTicked(): bool
    {
        return $this->ticked;
    }

    public function getOperatorDesc(): string
    {
        $name = static::class;
        $ucl = $this->_ucl->toEncodedStr();
        return "$name : $ucl";
    }

    public function getDialog(): Dialog
    {
        return $this;
    }

    /*-------- status --------*/

    public function isEvent(string $statusType): bool
    {
        return is_a($this, $statusType, TRUE);
    }


    public function remember(string $name): bool
    {
        return $this
            ->cloner
            ->mind
            ->memoryReg()
            ->hasDef($name);
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

    /**
     * @param Dialog $dialog
     * @return static
     */
    public function withPrev(Dialog $dialog): Dialog
    {
        $this->_prev = $dialog;
        return $this;
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

        return in_array($name, ['cloner', 'ucl', 'context']);
    }

    public function __get($name)
    {
        switch ($name) {
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