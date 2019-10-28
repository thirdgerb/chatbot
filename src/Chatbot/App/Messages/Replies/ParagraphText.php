<?php


namespace Commune\Chatbot\App\Messages\Replies;


use Commune\Chatbot\App\Messages\ReplyIds;
use Commune\Chatbot\Blueprint\Conversation\Speech;
use Commune\Chatbot\Blueprint\Message\ReplyMsg;
use Commune\Chatbot\Framework\Messages\AbsVerbose;
use Illuminate\Support\Collection;
use Commune\Chatbot\Blueprint\Message\Replies\Paragraph;

/**
 * 段落类型的text
 */
class ParagraphText extends AbsVerbose implements Paragraph
{

    protected $joint;

    /**
     * @var ReplyMsg[]
     */
    protected $sentences = [];

    /**
     * ParagraphText constructor.
     * @param string $joint
     * @param ReplyMsg[] $sentences
     */
    public function __construct(string $joint, array $sentences)
    {
        $this->sentences = $sentences;
        $this->joint = $joint;
        parent::__construct('');
    }

    public function __sleep(): array
    {
        return array_merge(parent::__sleep(), ['sentences']);
    }

    public function add(ReplyMsg $reply) : Paragraph
    {
        $this->sentences[] = $reply;
        $this->withLevel($reply->getLevel());
        return $this;
    }

    public function getReplies(): array
    {
        return $this->sentences;
    }

    public function withText(string ...$texts): Paragraph
    {
        $this->_text .= implode($this->joint, $texts);
        return $this;
    }


    public function isEmpty(): bool
    {
        return empty($this->sentences);
    }


    public function getText(): string
    {
        return $this->_text;
    }

    public function getReplyId(): string
    {
        return ReplyIds::PARAGRAPH;
    }

    public function getLevel(): string
    {
        return Speech::INFO;
    }

    public function getSlots(): Collection
    {
        return new Collection();
    }

    public function mergeSlots(array $slots): void
    {
        return;
    }

    public static function mock()
    {
        return (new static('', [ Reply::mock(), Link::mock()]))
            ->add(Reply::mock())
            ->add(Link::mock())
            ->add(Reply::mock());
    }
}