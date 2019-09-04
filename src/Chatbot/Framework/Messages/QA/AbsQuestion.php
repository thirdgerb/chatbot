<?php


namespace Commune\Chatbot\Framework\Messages\QA;


use Commune\Chatbot\Blueprint\Conversation\Speech;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Messages\AbsMessage;
use Illuminate\Support\Collection;

abstract class AbsQuestion extends AbsMessage implements Question
{
    const REPLY_ID = 'question';

    /**
     * @var string
     */
    protected $question;

    /**
     * @var array
     */
    protected $suggestions;

    /**
     * 预期用户输入的默认值. 一般是string或int
     * 也可能是message
     *
     * @var mixed
     */
    protected $default;

    /**
     * @var bool
     */
    protected $nullable;

    /**
     * @var Answer|null
     */
    protected $answer;

    /**
     * @var Collection
     */
    protected $slots;

    /**
     * AbsAsk constructor.
     * @param string $question
     * @param array $suggestions
     * @param mixed $default
     */
    public function __construct(
        string $question,
        array $suggestions,
        $default = null
    )
    {
        $this->question = $question;
        $this->suggestions = $suggestions;
        $this->default = $default;
        $this->nullable = isset($this->default);
        parent::__construct();
    }

    public function getQuery(): string
    {
        return $this->question;
    }


    abstract public function parseAnswer(Message $message): ? Answer;

    /**
     * default choice for default value
     * @return mixed|null
     */
    abstract public function getDefaultChoice();

    public function getText(): string
    {
        return $this->getQuery();
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function getSuggestions(): array
    {
        return $this->suggestions;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }


    public function toMessageData(): array
    {
        return [
            'id' => $this->getId(),
            'question' => $this->question,
            'suggestions' => $this->suggestions,
            'default' => $this->getDefaultValue(),
            'defaultChoice' => $this->getDefaultChoice(),
            'nullable' => $this->nullable,
        ];
    }

    public function namesAsDependency(): array
    {
        return array_merge(
            parent::namesAsDependency(),
            [Question::class, AbsQuestion::class]
        );
    }

    public function getId(): string
    {
        return static::REPLY_ID;
    }

    public function getLevel(): string
    {
        return Speech::INFO;
    }

    public function getSlots(): Collection
    {
        return $this->slots ?? $this->slots = new Collection($this->toMessageData());
    }


}