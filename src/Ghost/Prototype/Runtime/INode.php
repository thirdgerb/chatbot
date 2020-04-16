<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Runtime;

use Commune\Ghost\Blueprint\Runtime\Node;
use Commune\Support\Arr\ArrayAbleToJson;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class INode implements Node
{
    use ArrayAbleToJson;

    /**
     * @var string
     */
    protected $contextName;

    /**
     * @var string
     */
    protected $contextId;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @var string
     */
    protected $stageName = '';

    /**
     * @var string[]
     */
    protected $stack = [];

    /**
     * INode constructor.
     * @param string $contextName
     * @param string $contextId
     * @param int $priority
     */
    public function __construct(
        string $contextName,
        string $contextId,
        int $priority
    )
    {
        $this->contextName = $contextName;
        $this->contextId = $contextId;
        $this->priority = $priority;
    }

    public function getStageFullname(): string
    {
        $str = empty($this->stageName) ? '' : '.' . $this->stageName;
        return $this->contextName . $str;
    }



    public function next(): bool
    {
        $newStage = array_shift($this->stack);
        if (empty($newStage)) {
            return false;
        }
        $this->stageName = $newStage;
        return true;
    }

    public function pushStack(array $stageNames): void
    {
        $this->stack = array_merge($stageNames, $this->stack);
    }

    public function flushStack(): void
    {
        $this->stack = [];
    }


    public function reset(): void
    {
        $this->stageName = '';
        $this->stack = [];
    }


    public function __clone()
    {
    }

    public function __destruct()
    {
    }
}