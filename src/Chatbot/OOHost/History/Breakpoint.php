<?php


namespace Commune\Chatbot\OOHost\History;

use Commune\Chatbot\OOHost\Session\SessionData;
use Commune\Chatbot\OOHost\Session\SessionDataIdentity;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;

/**
 * @property-read  string $id
 * @property-read  string $conversationId
 * @property-read  string $sessionId
 * @property-read  string $prevId
 * @property-read  string[] $backtrace
 */
class Breakpoint implements ArrayAndJsonAble, SessionData, HasIdGenerator
{
    use ArrayAbleToJson, IdGeneratorHelper;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $conversationId;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var string
     */
    protected $prevId;

    /**
     * @var string[]
     */
    protected $backtrace = [];

    /**
     * @var Process
     */
    protected $process;

    /*----- cached -----*/

    /**
     * @var int
     */
    protected $maxHistory;

    public function __construct(
        string $conversationId,
        string $sessionId,
        int $maxBreakpointHistory,
        Process $process,
        string $prevId = null,
        array $backtrace = []
    )
    {
        $this->id = $conversationId;
        $this->sessionId = $sessionId;
        $this->maxHistory = $maxBreakpointHistory;
        $this->process = $process;
        $this->prevId = $prevId;
        $this->backtrace = $backtrace;

    }

    protected function pushPrev(string $breakPointId, int $num) : void
    {
        $this->backtrace[] = $breakPointId;
        if (count($this->backtrace) > $num) {
            array_shift($this->backtrace);
        }
    }

    public function process() : Process
    {
        return $this->process;
    }


    public function backward() : ? string
    {
        if (empty($this->backtrace)) {
            return null;
        }
        $lastId = end($this->backtrace);
        return $lastId;
    }

    public function replaceProcess(Process $process) : void
    {
        $this->process = $process;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'sessionId' => $this->sessionId,
            'prevId' => $this->prevId,
            'backtrace' => $this->backtrace,
            'process' => $this->process->toArray()
        ];
    }

    public function toSessionIdentity(): SessionDataIdentity
    {
        return new SessionDataIdentity(
            $this->getSessionDataId(),
            $this->getSessionDataType()
        );
    }

    public function shouldSave(): bool
    {
        return true;
    }

    public function getSessionDataType(): string
    {
        return SessionData::BREAK_POINT;
    }

    public function getSessionDataId(): string
    {
        return $this->id;
    }

    public function __sleep()
    {
        return [
            'id',
            'sessionId',
            'prevId',
            'backtrace',
            'process'
        ];
    }

    public function __get($name)
    {
        return $this->{$name};
    }

}