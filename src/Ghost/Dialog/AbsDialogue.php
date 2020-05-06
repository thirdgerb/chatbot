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
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Routing\Matcher;
use Commune\Blueprint\Ghost\Routing\Redirector;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Runtime\Task;
use Commune\Blueprint\Ghost\Typer;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\Traits\TRedirector;
use Commune\Support\DI\Injectable;
use Commune\Support\DI\TInjectable;


/**
 * 抽象的 Dialog 实现.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsDialogue implements Dialog, Injectable, Redirector
{
    use TInjectable, TRedirector;

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
        // TODO: Implement send() method.
    }

    public function matcher(): Matcher
    {
        return $this->cloner->container->make(Matcher::class);
    }

    public function then(): Redirector
    {
        return $this;
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