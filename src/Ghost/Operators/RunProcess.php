<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Ghost\Operators\Run;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RunProcess implements Operator
{

    /**
     * 启动的完整流程
     * @var string[]
     */
    protected $runners = [
        Run\YieldCheck::class,
        Run\RetainCheck::class,
        Run\Challenge::class,
        Run\ComprehendPipes::class,
        Run\WatchCheck::class,
        Run\StageRouting::class,
        Run\ContextRouting::class,
        Run\TryHeed::class,
        Run\TryWake::class,
        Confuse::class,
    ];

    protected $index = 0;

    /**
     * @var int
     */
    protected $count;

    public function __construct()
    {
        $this->count = count($this->runners);
    }

    public function invoke(Cloner $cloner): ? Operator
    {
        $process = $cloner->runtime->getCurrentProcess();
        $input = $cloner->ghostInput;

        $className = $this->runners[$this->index];
        /**
         * @var Run\Runner $operator
         */
        $operator = new $className($process, $input);

        $this->index ++;
        if ($this->index === $this->count) {
            return $operator;
        }

        // 如果有 Operator 就会中断当前的检查流程.
        return $operator->invoke($cloner) ?? $this;
    }


}