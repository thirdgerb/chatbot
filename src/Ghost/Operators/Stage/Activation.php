<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\Stage;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Routes\Activate as Contract;
use Commune\Blueprint\Ghost\Snapshot\Task;
use Commune\Ghost\Routes\Activate;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class Activation implements Operator
{

    /**
     * @var Task
     */
    protected $task;

    /**
     * @var string
     */
    protected $type;

    protected $activations = [
        Contract\Staging::class => Activate\IStaging::class,
        Contract\BackStep::class => Activate\IBackStep::class,
        Contract\Retain::class => Activate\IRetain::class,
    ];

    /**
     * Activation constructor.
     * @param Task $task
     * @param string $type
     */
    public function __construct(Task $task, string $type)
    {
        $this->task = $task;
        $this->type = $type;
    }

    public function invoke(Cloner $cloner): Operator
    {
        $wrapper = $this->activations[$this->type];
        $stageDef = $this->task->findStageDef($cloner);

        /**
         * @var Contract $activator
         */
        $activator = new $wrapper($cloner, $this->task);
        return $stageDef->onActivate($cloner, $activator);

    }


}