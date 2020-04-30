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

use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Snapshot\Task;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class DoFulfill implements Operator
{
    /**
     * @var Task
     */
    protected $task;

    /**
     * DoFulfill constructor.
     * @param Task $task
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }


}