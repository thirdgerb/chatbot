<?php


namespace Commune\Chatbot\OOHost\History;

use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * @property-read  string $conversationId
 * @property-read  string $sessionId
 */
class Breakpoint implements ArrayAndJsonAble
{
    use ArrayAbleToJson;

    /**
     * @var string
     */
    protected $conversationId;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var Process
     */
    protected $process;

    /*----- cached -----*/

    public function __construct(
        string $conversationId,
        string $sessionId,
        Process $process
    )
    {
        $this->conversationId = $conversationId;
        $this->sessionId = $sessionId;
        $this->process = $process;
    }

    public function process() : Process
    {
        return $this->process;
    }

    public function replaceProcess(Process $process) : void
    {
        $this->process = $process;
    }

    public function toArray(): array
    {
        return [
            'sessionId' => $this->sessionId,
            'conversationId' => $this->conversationId,
            'process' => $this->process->toArray(),
        ];
    }

    public function __sleep()
    {
        return [
            'conversationId',
            'sessionId',
            'process'
        ];
    }

    public function __get($name)
    {
        return $this->{$name};
    }

}