<?php


namespace Commune\Chatbot\Framework\Messages;

use Commune\Chatbot\Blueprint\Message\ReplyMsg;
use Illuminate\Support\Collection;
use Commune\Chatbot\Blueprint\Conversation\Speech;

abstract class AbsReply extends AbsVerbose implements ReplyMsg
{
    /**
     * @var string
     */
    protected $_id;

    /**
     * @var Collection
     */
    protected $_slots;

    public function __construct(
        string $id,
        Collection $slots = null,
        string $level = Speech::INFO
    )
    {
        $this->_id = $id;
        $this->_slots = $slots;
        $this->_level = $level;
        parent::__construct('');
    }

    public function __sleep(): array
    {
        return array_merge(parent::__sleep(), ['_id', '_slots']);
    }


    public function getReplyId(): string
    {
        return $this->_id;
    }

    public function getLevel(): string
    {
        return $this->_level;
    }

    public function getSlots(): Collection
    {
        return $this->_slots
            ?? $this->_slots = new Collection();
    }

    public function mergeSlots(array $slots): void
    {
        $this->_slots = $this->getSlots()->merge($slots);
    }


    public function isEmpty(): bool
    {
        return '' === $this->_id;
    }

    public function getText(): string
    {
        return $this->_id;
    }


}