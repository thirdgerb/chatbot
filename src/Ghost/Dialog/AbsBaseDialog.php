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
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Memory\Recollection;
use Commune\Blueprint\Ghost\Tools\Hearing;
use Commune\Blueprint\Ghost\Tools\Matcher;
use Commune\Blueprint\Ghost\Tools\Navigator;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Tools\Caller;
use Commune\Blueprint\Ghost\Tools\Deliver;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Tools\IDeliver;
use Commune\Ghost\Tools\IHearing;
use Commune\Support\DI\Injectable;
use Commune\Support\DI\TInjectable;
use Commune\Blueprint\Exceptions\HostLogicException;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Exceptions\Runtime\BrokenRequestException;


/**
 * 抽象的 Dialog 实现.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsBaseDialog implements
    Dialog,
    Injectable,
    Navigator,
    Caller
{
    use TInjectable;

    /*------ params -------*/

    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * @var Ucl
     */
    protected $ucl;

    /**
     * @var Dialog|null
     */
    protected $prev;


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
     * @param AbsBaseDialog|null $prev
     */
    public function __construct(Cloner $cloner, Ucl $ucl, AbsBaseDialog $prev = null)
    {
        $this->cloner = $cloner;
        $this->ucl = $ucl->toInstance($cloner);
        $this->prev = $prev;
        $this->stack = isset($prev) ? $prev->dumpStack() : [];
    }

    /*-------- implements --------*/

    public function send(): Deliver
    {
        return new IDeliver($this);
    }

    public function matcher(): Matcher
    {
        return $this->cloner->matcher->refresh();
    }

    public function nav(): Navigator
    {
        return $this;
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
        return $this->cloner->container->make($abstract, $parameters);
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
            return $this->cloner->container->call($caller, $parameters);
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

    public function action(callable $caller): ? Dialog
    {
        $result = $this->call($caller);
        if (is_null($result) || $result instanceof Dialog) {
            return $result;
        }

        throw new InvalidArgumentException('caller is not predict which return with bool');
    }

    protected function getContextualInjections(array $parameters) : array
    {
        $injections = [
            'context' => $this->context,
            'prev' => $this->prev,
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


    /*-------- tick --------*/

    protected function dumpStack() : array
    {
        $stack = $this->stack;
        $this->stack = [];
        return $stack;
    }

    protected function pushStack(callable $caller) : void
    {
        $this->stack[] = $caller;
    }


    public function tick(): Dialog
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
        if ($this instanceof Dialog\Intercept && isset($this->prev)) {
            $stageDef = $this->ucl->findStageDef($this->cloner);
            $next = $stageDef->onIntercept($this->prev);
        }

        // 未被拦截的时候.
        if (!isset($next)) {
            // 下一次 tick 关闭上一次tick
            $prev = $this->prev;
            if (isset($prev)) {
                $prev->ticked = true;
            }

            $this->runStack();

            $next = $this->runTillNext();
        }

        $this->ticking = false;
        return $next;
    }

    protected function runStack() : void
    {
        $stack = $this->dumpStack();
        while($caller = array_shift($stack)) {
            $caller($this);
        }
    }



    /*-------- inner --------*/

    /**
     * 寻找到下一个 Intend 的对象.
     * @return static
     */
    abstract protected function runTillNext() : Dialog;

    /**
     * @return Process
     */
    protected function getProcess() : Process
    {
        return $this->process
            ?? $this->process = $this->cloner->runtime->getCurrentProcess();
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
            ->recall($this->cloner);
    }

    /**
     * @param Dialog $dialog
     * @return static
     */
    public function withPrev(Dialog $dialog): Dialog
    {
        $this->prev = $dialog;
        return $this;
    }


    /*-------- getter --------*/

    public function __isset($name)
    {
        if ($name === 'prev') {
            return isset($this->prev);
        }

        return in_array($name, ['cloner', 'ucl', 'context']);
    }

    public function __get($name)
    {
        switch ($name) {
            case 'cloner' :
                return $this->cloner;
            case 'ucl' :
                return $this->ucl;
            case 'prev' :
                return $this->prev;
            case 'context' :
                return $this->ucl->findContext($this->cloner);
            default:
                return null;
        }

    }

    public function __destruct()
    {
        $prev = $this->prev;
        unset($this->prev);

        if ($prev instanceof self) {
            $prev->__destruct();
        }

        $this->cloner = null;
        $this->ucl = null;
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