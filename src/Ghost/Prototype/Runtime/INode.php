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

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Definition\ContextDef;
use Commune\Ghost\Blueprint\Definition\StageDef;
use Commune\Ghost\Blueprint\Runtime\Node;
use Commune\Ghost\Blueprint\Runtime\Thread;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $contextId         当前节点所属语境 id
 * @property-read string $contextName       当前节点所属的语境名称
 * @property-read int $priority             当前语境的优先级
 * @property-read string $stageName         当前节点所属的 stage 名称
 * @property-read string[] $stack        接下来要经过的 stage
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
        return StringUtils::gluePrefixAndName($this->contextName, $this->stageName, Context::NAMESPACE_SEPARATOR);
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

    public function toThread(): Thread
    {
        return new IThread($this);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function findContextDef(Conversation $conversation): ContextDef
    {
        return $conversation->mind->contextReg()->getDef($this->contextName);
    }

    public function findStageDef(Conversation $conversation): StageDef
    {
        return $conversation->mind->stageReg()->getDef($this->getStageFullname());
    }

    public function findContext(Conversation $conversation) : Context
    {
        return $conversation->runtime->findContext($this->contextId)
            ?? $conversation->newContext($this->contextName, null);
    }

    public function __get($name)
    {
        return $this->{$name};
    }

}