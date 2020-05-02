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

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Definition\StageDef;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Routes\Intercept\Intend;
use Commune\Blueprint\Ghost\Routes\Intercept\Watch;
use Commune\Blueprint\Ghost\Snapshot\Task;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class Interception implements Operator
{

    /**
     * @var StageDef
     */
    protected $stageDef;

    /**
     * @var Task
     */
    protected $current;


    /**
     * @var Context
     */
    protected $self;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string[]
     */
    protected $interceptions = [
        Intend::class,
        Watch::class,
    ];

    /**
     * Interception constructor.
     * @param Task $interceptor
     * @param string $stageFullName
     * @param string $type
     */
    public function __construct(Task $interceptor, string $stageFullName, string $type)
    {
    }


}