<?php


namespace Commune\Chatbot\OOHost\History;

use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionData;
use Commune\Chatbot\OOHost\Session\SessionDataIdentity;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Arr\ArrayAndJsonAble;

class Breakpoint implements ArrayAndJsonAble, SessionData
{
    use ArrayAbleToJson;

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $sessionId;

    /**
     * @var string
     */
    public $prevId;

    /**
     * @var string[]
     */
    public $backtrace = [];

    /**
     * @var Question|null
     */
    public $prevQuestion;

    /**
     * @var Question|null
     */
    public $question;

    /**
     * @var Process
     */
    public $process;

    /**
     * @var int
     */
    public $maxHistory;

    public function __construct(
        Session $session,
        Breakpoint $prev = null
    )
    {
        $this->id = $session->conversation->getConversationId();
        $this->sessionId = $session->sessionId;
        $this->maxHistory = $session->hostConfig->maxBreakpointHistory;

        if (isset($prev)) {
            $this->fromPrev($prev);
        } else {
            $this->fromRoot($session);
        }
    }

    protected function fromPrev(Breakpoint $prev) : void
    {
        $this->prevId = $prev->getSessionDataId();
        $question = $prev->question;
        if (isset($question)) {
            $this->prevQuestion = clone $question;
        }
        $this->process = $prev->process;

        if (isset($prev->prevId)) {
            $this->pushPrev(
                $prev->prevId,
                $this->maxHistory
            );
        }
    }

    protected function fromRoot(Session $session) : void
    {
        $this->process = new Process($session);
    }

    protected function pushPrev(string $breakPointId, int $num) : void
    {
        $this->backtrace[] = $breakPointId;
        if (count($this->backtrace) > $num) {
            array_shift($this->backtrace);
        }
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'sessionId' => $this->sessionId,
            'prevId' => $this->prevId,
            'backtrace' => $this->backtrace,
            'prevQuestion' => isset($this->prevQuestion)
                ? $this->prevQuestion->toArray()
                : null,
            'question' => isset($this->question)
                ? $this->question->toArray()
                : null,
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
            'question',
            'process'
        ];
    }

}