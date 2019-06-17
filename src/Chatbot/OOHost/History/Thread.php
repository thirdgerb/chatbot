<?php


namespace Commune\Chatbot\OOHost\History;


use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Arr\ArrayAndJsonAble;

class Thread implements ArrayAndJsonAble
{
    use ArrayAbleToJson;

    /**
     * @var Node[]
     */
    public $stacks = [];

    /**
     * @var Node
     */
    public $node;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    public function currentNode() : Node
    {
        return $this->node;
    }

    public function pop() : ? Node
    {
        $node = array_pop($this->stacks);
        if (isset($node)) {
            $this->node = $node;
            return $node;
        }
        return null;
    }

    public function push(Node $node) : void
    {
        array_push($this->stacks, $this->node);
        $this->node = $node;
    }

    public function toArray(): array
    {
        $stack = [];
        foreach ($this->stacks as $key => $node) {
            $stack[$key] = $node->toArray();
        }
        return [
            'node' => $this->node->toArray(),
            'stack' => $stack
        ];
    }
}