<?php


namespace Commune\Chatbot\App\Messages;


use Commune\Chatbot\Blueprint\Conversation\Speech;
use Commune\Chatbot\Blueprint\Message\ReplyMsg;
use Commune\Chatbot\Blueprint\Message\Tags\SelfTranslating;
use Commune\Chatbot\Contracts\Translator;
use Commune\Chatbot\Framework\Messages\AbsMessage;
use Commune\Chatbot\Framework\Messages\Reply;
use Illuminate\Support\Collection;

class ParagraphText extends AbsMessage implements ReplyMsg, SelfTranslating
{

    /**
     * @var Reply[]
     */
    protected $sentences = [];

    /**
     * Paragraph constructor.
     * @param Reply[] $sentences
     */
    public function __construct(array $sentences)
    {
        $this->sentences = $sentences;
        parent::__construct();
    }

    public function add(Reply $reply) : ParagraphText
    {
        $this->sentences[] = $reply;
        return $this;
    }

    public function translateBy(Translator $translator): array
    {
        $text = '';
        foreach ($this->sentences as $reply) {
            $text .= $translator->trans($reply->getReplyId(), $reply->getSlots()->all());
        }

        return [new Text($text)];
    }

    public function isEmpty(): bool
    {
        return empty($this->sentences);
    }

    public function toMessageData(): array
    {
        return [
            'sentences' => array_map(function(Reply $sentence){
                return $sentence->toMessageData();
            }, $this->sentences),
        ];
    }

    public function getText(): string
    {
        if (isset($this->_text)) {
            return $this->_text;
        }

        $this->_text = '';
        foreach ($this->sentences as $sentence) {
            $this->_text .= $sentence->getReplyId();
        }

        return $this->_text;
    }

    public function getReplyId(): string
    {
        return '';
    }

    public function getLevel(): string
    {
        return Speech::INFO;
    }

    public function getSlots(): Collection
    {
        return new Collection();
    }

    public function withSlots(array $slots): void
    {
        return;
    }


}