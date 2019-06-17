<?php


namespace Commune\Chatbot\OOHost\History;


use Commune\Chatbot\OOHost\Session\Session;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Arr\ArrayAndJsonAble;

class Process implements ArrayAndJsonAble
{
    use ArrayAbleToJson;

    public $sessionId;

    /**
     * @var Thread
     */
    public $thread;

    /**
     * @var Thread[]
     */
    public $sleeping = [];

    public function __construct(Session $session)
    {
        $this->sessionId = $session->sessionId;

        $root = $session->makeRootContext();
        $this->thread = new Thread(new Node($root));
    }

    public function sleepTo(Thread $thread) : void
    {
        array_push($this->sleeping, $this->thread);
        $this->thread = $thread;
    }

    public function pop() : ? Thread
    {
        return array_pop($this->sleeping);
    }

    public function toArray(): array
    {
        $sleeping = [];
        $yielding = [];

        foreach ($this->sleeping as $key => $value) {
            $sleeping[$key] = $value->toArray();
        }

        return [
            'sessionId' => $this->sessionId,
            'thread' => $this->thread->toArray(),
            'sleeping' => $sleeping,
            'yielding' => $yielding
        ];
    }
}