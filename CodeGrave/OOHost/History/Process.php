<?php


namespace Commune\Chatbot\OOHost\History;


use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Arr\ArrayAndJsonAble;

class Process implements ArrayAndJsonAble
{
    use ArrayAbleToJson;

    protected $sessionId;

    /**
     * @var Thread
     */
    protected $thread;

    /**
     * @var Thread[]
     */
    protected $sleeping = [];

    public function __construct(string $sessionId, Thread $thread)
    {
        $this->sessionId = $sessionId;
        $this->thread = $thread;
    }

    public function currentThread() : Thread
    {
        return $this->thread;
    }

    public function currentTask() : Node
    {
        return $this->thread->currentTask();
    }

    public function currentQuestion() : ? Question
    {
        return $this->thread->currentQuestion();
    }


    public function setQuestion(Question $question = null) : void
    {
        $this->thread->setQuestion($question);
    }


    public function goStage(string $stageName, bool $reset) : void
    {
        $this->thread->goStage($stageName, $reset);
    }


    public function addStage(string $stage)  : void
    {
        $this->thread->addStage($stage);
    }

    public function nextStage() : ? string
    {
        return $this->thread->nextStage();
    }

    public function replaceThread(Thread $thread) : void
    {
        $this->thread = $thread;
    }


    public function replaceNode(Node $task)  :void
    {
        $this->thread->replaceNode($task);
    }


    public function dependOn(Node $node) : void
    {
        $this->thread->dependOn($node);
    }

    public function sleepTo(Thread $thread) : void
    {
        array_push($this->sleeping, $this->thread);
        $this->thread = $thread;
    }

    public function intended() : ? Node
    {
        return $this->thread->intended();
    }

    public function wake() : ? Thread
    {
        return array_pop($this->sleeping);
    }

    public function toArray(): array
    {
        $sleeping = [];
        foreach ($this->sleeping as $key => $value) {
            $sleeping[$key] = $value->toArray();
        }

        return [
            'sessionId' => $this->sessionId,
            'thread' => $this->thread->toArray(),
            'sleeping' => $sleeping,
        ];
    }

    public function __clone()
    {
        $this->thread = clone $this->thread;

        foreach ($this->sleeping as $index => $sleeping) {
            $this->sleeping[$index] = clone $sleeping;
        }
    }

    /**
     * @return array
     */
    public function getContextIds()
    {
        $ids = $this->thread->getContextIds();
        foreach ($this->sleeping as $thread) {
            $ids = array_merge($ids, $thread->getContextIds());
        }
        return $ids;
    }
}