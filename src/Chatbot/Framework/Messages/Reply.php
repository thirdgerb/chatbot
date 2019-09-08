<?php


namespace Commune\Chatbot\Framework\Messages;

use Commune\Chatbot\Blueprint\Message\ReplyMsg;
use Illuminate\Support\Collection;
use Commune\Chatbot\Blueprint\Conversation\Speech;

class Reply extends AbsMessage implements ReplyMsg
{
    const MESSAGE_TYPE = ReplyMsg::class;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var Collection
     */
    protected $slots;

    /**
     * @var string
     */
    protected $level;

    public function __construct(
        string $id,
        Collection $slots = null,
        string $level = Speech::INFO
    )
    {
        $this->id = $id;
        $this->slots = $slots;
        $this->level = $level;
        parent::__construct(null);
    }


    public function getId(): string
    {
        return $this->id;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getSlots(): Collection
    {
        return $this->slots
            ?? $this->slots = new Collection();
    }


    public function isEmpty(): bool
    {
        return empty($this->id);
    }

    public function getText(): string
    {
        return $this->id;
    }

    public function toMessageData(): array
    {
        return [
            'id' => $this->id,
            'level' => $this->level,
            'slots' => $this->slots->toArray()
        ];
    }

    public function namesAsDependency(): array
    {
        $names = parent::namesAsDependency();
        $names[] = ReplyMsg::class;
        return $names;
    }


}