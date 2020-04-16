<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\OperatorsBack\Process;

use Commune\Ghost\Blueprint\Runtime\Process;
use Commune\Ghost\Prototype\OperatorsBack\AbsOperator;


/**
 * 让进程开始处理消息
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ProcessOnHear extends AbsOperator
{
    /**
     * @var Process
     */
    protected $process;

    /**
     * ProcessOnHear constructor.
     * @param Process $process
     */
    public function __construct(Process $process)
    {
        $this->process = $process;
    }



}