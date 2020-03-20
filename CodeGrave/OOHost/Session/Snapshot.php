<?php


namespace Commune\Chatbot\OOHost\Session;

use Commune\Chatbot\OOHost\History\Breakpoint;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Support\Utils\ArrayUtils;

/**
 * Session 上下文信息的快照.
 */
class Snapshot implements ArrayAndJsonAble
{
    use ArrayAbleToJson;

    /**
     * @var string
     */
    public $sessionId;

    /**
     * @var string
     */
    public $belongsTo;

    /**
     * @var Breakpoint|null
     */
    public $breakpoint;

    /**
     * @var Breakpoint|null
     */
    public $prevBreakpoint;

    /**
     * @var Breakpoint[]
     */
    public $backtrace =[];

    /**
     * 如果没有saved, 则可能出错了.
     * @var bool
     */
    public $saved = false;

    /**
     * Snapshot constructor.
     * @param string $sessionId
     * @param string $belongsTo
     */
    public function __construct(string $sessionId, string $belongsTo)
    {
        $this->belongsTo = $belongsTo;
        $this->sessionId = $sessionId;
    }

    public function getContextIds() : array
    {
        $ids = $this->breakpoint->process()->getContextIds();
        if (isset($this->prevBreakpoint)) {
            $ids = array_merge(
                $ids,
                $this->prevBreakpoint->process()->getContextIds()
            );
        }

        foreach ($this->backtrace as $breakpoint) {
            $ids = array_merge(
                $ids,
                $breakpoint->process()->getContextIds()
            );
        }

        return array_unique($ids);
    }

    public function getBreakpointIds() : array
    {
        $ids = [];
        $ids['current'] = $this->breakpoint->conversationId;
        if (isset($this->prevBreakpoint)) {
            $ids['prev'] = $this->prevBreakpoint->conversationId;
        }

        $ids['backtrace'] = [];
        foreach ($this->backtrace as $breakpoint) {
            $ids['backtrace'][] = $breakpoint->conversationId;
        }

        return $ids;
    }

    public function toArray(): array
    {
        return ArrayUtils::recursiveToArray((array) $this);
    }


}