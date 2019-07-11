<?php


namespace Commune\Chatbot\OOHost\History;


use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Arr\ArrayAndJsonAble;

class Thread implements ArrayAndJsonAble
{
    use ArrayAbleToJson;

    /**
     * @var Node[]
     */
    protected $stacks = [];

    /**
     * @var Node
     */
    protected $node;

    /**
     * @var Question
     */
    protected $question;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    public function currentTask() : Node
    {
        return $this->node;
    }

    public function currentQuestion() : ? Question
    {
        return $this->question;
    }


    public function setQuestion(Question $question = null) : void
    {
        $this->question = $question;
    }


    public function goStage(string $stageName, bool $reset) : void
    {
        $this->node->goStage($stageName, $reset);
        $this->question = null;
    }


    public function addStage(string $stage)  : void
    {
        $this->node->addStage($stage);
        $this->question = null;
    }


    public function nextStage() : ? string
    {
        $next = $this->node->nextStage();
        if (isset($next)) {
            $this->question = null;
        }
        return $next;
    }


    public function replaceNode(Node $task)  :void
    {
        $this->node = $task;
        $this->question = null;
    }

    public function dependOn(Node $node) : void
    {
        array_push($this->stacks, $this->node);
        $this->node = $node;
        $this->question = null;
    }

    public function intended() : ? Node
    {
        $node = array_pop($this->stacks);
        if (isset($node)) {
            $this->node = $node;
            return $node;
        }
        return null;
    }


    public function toArray(): array
    {
        $stack = [];
        foreach ($this->stacks as $key => $node) {
            $stack[$key] = $node->toArray();
        }
        return [
            'node' => $this->node->toArray(),
            'question' => isset($this->question) ? $this->question->toArray() : null,
            'stack' => $stack
        ];
    }


    public function __clone()
    {
        $this->node = clone $this->node;
        $this->question = isset($this->question) ? clone $this->question : null;
        foreach ($this->stacks as $index => $stack) {
            $this->stacks[$index] = clone $stack;
        }
    }
}