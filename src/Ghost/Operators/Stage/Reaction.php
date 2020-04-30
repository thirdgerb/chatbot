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

use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Routes\React\Heed;
use Commune\Blueprint\Ghost\Routes\React\Restore;
use Commune\Blueprint\Ghost\Routes\React\Wake;
use Commune\Blueprint\Ghost\Snapshot\Task;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class Reaction implements Operator
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
        Heed::class,
        Restore::class,
        Wake::class,
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


}