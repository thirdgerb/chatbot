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
use Commune\Blueprint\Ghost\Dialogue;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\Traits\TEscape;
use Commune\Support\DI\Injectable;
use Commune\Support\DI\TInjectable;


/**
 * 抽象的 Dialog 实现.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsDialogue implements Dialog, Injectable
{
    use TEscape, TInjectable;

    const ACTIVATOR = [

    ];

    const ESCAPER = [

    ];

    const RETAINER = [

    ];

    /*------ cached -------*/

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

    /**
     * @var Process|null
     */
    protected $process;

    /**
     * @var bool
     */
    protected $ticked = false;



    /**
     * AbsDialogue constructor.
     * @param Cloner $cloner
     * @param Ucl $ucl
     * @param Dialog|null $prev
     */
    public function __construct(Cloner $cloner, Ucl $ucl, Dialog $prev = null)
    {
        $this->cloner = $cloner;
        $this->ucl = $ucl;
        $this->prev = $prev;
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

        // 正式运行的时候, 必须把当前 Task 设置成为 alive 的对象.
        $this->selfActivate();

        $next = $this->buildNext();
        $this->ticked = true;

        return $next;
    }

    /*-------- buildDialog --------*/

    /**
     * @param Ucl $ucl
     * @param string $dialogInterface
     * @return Dialogue\Escape|static
     */
    protected function buildEscaper(Ucl $ucl, string $dialogInterface) : Dialogue\Escape
    {
        $class = static::ESCAPER[$dialogInterface];
        return new $class($this->cloner, $ucl, $this);
    }

    /**
     * @param Ucl $ucl
     * @param string $dialogInterface
     * @return Dialogue\Activate|static
     */
    protected function buildActivator(Ucl $ucl, string $dialogInterface) : Dialogue\Activate
    {
        $class = static::ACTIVATOR[$dialogInterface];
        return new $class($this->cloner, $ucl, $this);
    }

    /**
     * @param Ucl $ucl
     * @param string $dialogInterface
     * @return Dialogue\Retain|static
     */
    protected function buildRetainer(Ucl $ucl, string $dialogInterface) : Dialogue\Retain
    {
        $class = static::RETAINER[$dialogInterface];
        return new $class($this->cloner, $ucl, $this);
    }



    /*-------- inner --------*/

    /**
     * 寻找到下一个 Intend 的对象.
     * @return static
     */
    abstract protected function buildNext() : Dialog;


    /**
     * 将当前 Process 的状态变更.
     */
    abstract protected function selfActivate() : void;

    protected function getProcess() : Process
    {
        return $this->process
            ?? $this->process = $this->cloner->runtime->getCurrentProcess();
    }

    /*-------- quit --------*/

    /**
     * 尝试退出整个会话.
     * @return Dialog
     */
    public function quit(): Dialog
    {
            // 退出依赖
        return $this->escapeDepended(Dialogue\Escape\Quit::class)
            // 退出阻塞
            ?? $this->escapeBlocking(Dialogue\Escape\Quit::class)
            // 退出睡眠
            ?? $this->escapeSleeping(Dialogue\Escape\Quit::class)
            // 退出监视
            ?? $this->escapeWatching(Dialogue\Escape\Quit::class)
            // 退出全部
            ?? $this->closeSession($this->ucl);
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
                    ?? $this->curContext = $this->cloner->getContext($this->ucl);
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