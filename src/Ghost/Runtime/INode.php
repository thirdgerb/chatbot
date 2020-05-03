<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Runtime;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Definition\ContextDef;
use Commune\Blueprint\Ghost\Definition\StageDef;
use Commune\Blueprint\Ghost\Runtime\Node;
use Commune\Blueprint\Ghost\Runtime\Thread;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Utils\StringUtils;
use Commune\Blueprint\Ghost\Cloner;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $contextId         当前节点所属语境 id
 * @property-read string $contextName       当前节点所属的语境名称
 * @property-read int $priority             当前语境的优先级
 * @property-read string $stageName         当前节点所属的 stage 名称
 * @property-read string[] $next        接下来要经过的 stage
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
    protected $next = [];

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
        return StringUtils::gluePrefixAndName(
            $this->contextName,
            $this->stageName,
            Context::NAMESPACE_SEPARATOR
        );
    }

    public function next(): bool
    {
        $newStage = array_shift($this->next);
        if (empty($newStage)) {
            return false;
        }
        $this->stageName = $newStage;
        return true;
    }

    public function pushStack(array $stageNames): void
    {
        $this->next = array_merge($stageNames, $this->next);
    }

    public function flushStack(): void
    {
        $this->next = [];
    }


    public function reset(): void
    {
        $this->stageName = '';
        $this->next = [];
    }

    public function toThread(): Thread
    {
        return new IThread($this);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function findContextDef(Cloner $cloner): ContextDef
    {
        return $cloner->mind->contextReg()->getDef($this->contextName);
    }

    public function findStageDef(Cloner $cloner): StageDef
    {
        return $cloner->mind->stageReg()->getDef($this->getStageFullname());
    }

    public function findContext(Cloner $cloner) : Context
    {
        return $cloner->getContext($this->contextId, $this->contextName);
    }

    public function __get($name)
    {
        return $this->{$name};
    }

}