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

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Runtime\Node;
use Commune\Blueprint\Ghost\Runtime\Thread;
use Commune\Protocals\Host\Convo\QuestionMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $id  Thread 的唯一ID, 由 Root 决定
 * @property-read int $priority 当前 Thread 的优先级
 * @property-read Node[] $nodes
 */
class IThread implements Thread
{
    /**
     * @var Node[]
     */
    protected $nodes = [];

    /**
     * @var string
     */
    protected $id;

    /**
     * @var null|QuestionMsg
     */
    protected $question;

    /**
     * @var bool
     */
    protected $expire = false;

    /**
     * @var int
     */
    protected $gcTurns = 0;

    public function __construct(Node $node)
    {
        $this->id = $node->contextId;
        $this->nodes[] = $node;
    }

    public function currentNode(): Node
    {
        return current($this->nodes);
    }

    public function replaceNode(Node $node): void
    {
        array_shift($this->nodes);
        if (empty($this->nodes)) {
            $this->id = $node->contextId;
        }
        array_unshift($this->nodes, $node);
    }

    public function pushNode(Node $node): void
    {
        array_unshift($this->nodes, $node);
    }

    public function popNode(): ? Node
    {
        if (count($this->nodes) > 1) {
            return array_shift($this->nodes);
        }

        return null;
    }

    public function getDescription(Cloner $cloner): string
    {
        $node = $this->currentNode();
        $contextDef = $node->findContextDef($cloner);
        return $contextDef->getDescription();
    }

    public function getQuestion(): ? QuestionMsg
    {
        return $this->question;
    }

    public function setQuestion(QuestionMsg $questionMsg): void
    {
        $this->question = $questionMsg;
    }

    /*------ cachable ------*/

    public function isCaching(): bool
    {
        return !$this->isExpiring();
    }

    public function expire(): void
    {
        $this->expire = true;
    }

    public function isExpiring(): bool
    {
        return $this->expire;
    }

    public function getCachableId(): string
    {
        return 'thread:'. $this->id;
    }

    /*------ gc ------*/

    public function setGc(int $turns)
    {
        $this->gcTurns = $turns;
    }

    public function gc(): bool
    {
        if ($this->gcTurns > 0) {
            $this->gcTurns--;
        }
        return $this->gcTurns === 0;
    }


    /*------ getter ------*/

    public function __get($name)
    {
        switch($name) {
            case 'id' :
                return $this->id;
            case 'nodes' :
                return $this->nodes;
            case 'priority' :
                return $this->currentNode()->priority;
            default:
                return null;
        }
    }

    public function __sleep()
    {
        return [
            'id',
            'node',
            'question',
            'gcTurns'
        ];
    }

    public function __clone()
    {
        $this->nodes = array_map(function($node){ return clone $node;}, $this->nodes);
        $this->question = clone $this->question;
    }

    public function __destruct()
    {
        $this->nodes = [];
        $this->question = null;
    }

}