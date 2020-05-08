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

use Commune\Blueprint\Exceptions\HostLogicException;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Exceptions\TooManyRedirectsException;
use Commune\Blueprint\Ghost\Tools\Hearing;
use Commune\Blueprint\Ghost\Tools\Matcher;
use Commune\Blueprint\Ghost\Tools\Navigator;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Tools\DialogIoC;
use Commune\Blueprint\Ghost\Tools\Typer;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\Traits\TRedirector;
use Commune\Ghost\Dialog\Traits\TWithdraw;
use Commune\Ghost\Tools\ITyper;
use Commune\Support\DI\Injectable;
use Commune\Support\DI\TInjectable;


/**
 * 抽象的 Dialog 实现.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsDialogue implements
    Dialog,
    Injectable,
    Navigator,
    DialogIoC
{
    use TInjectable, TRedirector, TWithdraw;

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
     * @var Context|null
     */
    protected $curContext;

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
     * AbsDialogue constructor.
     * @param Cloner $cloner
     * @param Ucl $ucl
     */
    public function __construct(Cloner $cloner, Ucl $ucl)
    {
        $this->cloner = $cloner;
        $this->ucl = $ucl;
    }

    /**
     * @param Dialog $dialog
     * @return static
     */
    public function withPrev(Dialog $dialog) : Dialog
    {
        $this->prev = $dialog;
        return $this;
    }

    /*-------- implements --------*/

    public function send(): Typer
    {
        return new ITyper($this);
    }

    public function matcher(): Matcher
    {
        return $this->cloner->container->make(Matcher::class);
    }

    public function then(): Navigator
    {
        return $this;
    }

    public function hearing(): Hearing
    {
        // TODO: Implement hearing() method.
    }

    public function getContext(Ucl $ucl): Context
    {
        return $this->cloner->getContext($ucl);
    }

    public function getUcl(string $contextOrUclStr, array $query = []): Ucl
    {
        $ucl = Ucl::decodeUcl($contextOrUclStr);

        return Ucl::create(
            $this->cloner,
            $ucl->contextName,
            $ucl->stageName,
            $query + $ucl->query
        );
    }

    /*-------- history --------*/

    public function depth(): int
    {
        $current = $this;
        $depth = 1;
        $max = $this->cloner->ghost->getConfig()->maxRedirectTimes;

        while($depth < $max && isset($current)) {
            $current = $current->prev;
            $depth++;
        }

        if ($depth >= $max) {
            throw new TooManyRedirectsException($max);
        }

        return $depth;
    }


    /*-------- app --------*/

    public function ioc(): DialogIoC
    {
        return $this;
    }


    public function make(string $abstract, array $parameters = [])
    {
        $parameters = $this->getContextualInjections($parameters);
        return $this->cloner->container->make($abstract, $parameters);
    }

    public function call(callable $caller, array $parameters = [])
    {
        $parameters = $this->getContextualInjections($parameters);
        return $this->cloner->container->call($caller, $parameters);
    }

    public function predict(callable $caller): bool
    {
        $result = $this->call($caller);
        if (!is_bool($result)) {
            throw new InvalidArgumentException(__METHOD__, 'caller', 'caller is not predict which return with bool');
        }

        return $result;
    }

    public function action(callable $caller): ? Dialog
    {
        $result = $this->call($caller);
        if (is_null($result) || $result instanceof Dialog) {
            return $result;
        }

        throw new InvalidArgumentException(__METHOD__, 'caller', 'caller is not predict which return with bool');
    }

    protected function getContextualInjections(array $parameters) : array
    {
        $injections = [
            'context' => $this->context,
            'prev' => $this->prev,
            'dialog' => $this,
        ];

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

        $next = $this->runInterception();

        // 未被拦截的时候.
        if (!isset($next)) {
            // 下一次 tick 关闭上一次tick
            $prev = $this->prev;
            if (isset($prev) && $prev instanceof self) {
                $prev->ticked = true;
            }

            $this->selfActivate();
            $next = $this->runTillNext();
        }

        $this->ticking = false;

        // 填补关联关系.
        $prev = $next->prev;
        if (!isset($prev)) {
            $next->prev = $this;
        }

        return $next;
    }



    /*-------- inner --------*/

    abstract protected function runInterception() : ? Dialog;

    /**
     * 寻找到下一个 Intend 的对象.
     * @return static
     */
    abstract protected function runTillNext() : Dialog;


    /**
     * 将当前 Process 的状态变更.
     */
    abstract protected function selfActivate() : void;

    /**
     * @return Process
     */
    protected function getProcess() : Process
    {
        return $this->process
            ?? $this->process = $this->cloner->runtime->getCurrentProcess();
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
                return $this->curContext
                    ?? $this->curContext = $this->getContext($this->ucl);
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
        $this->curContext = null;
        $this->process = null;
    }

    public function getInterfaces(): array
    {
        return static::getInterfacesOf(Dialog::class, false);
    }


}