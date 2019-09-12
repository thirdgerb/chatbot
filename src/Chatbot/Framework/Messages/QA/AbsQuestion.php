<?php


namespace Commune\Chatbot\Framework\Messages\QA;


use Commune\Chatbot\Blueprint\Conversation\Speech;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Framework\Messages\AbsMessage;
use Commune\Chatbot\OOHost\Session\Session;
use Illuminate\Support\Collection;

abstract class AbsQuestion extends AbsMessage implements Question
{
    const REPLY_ID = 'question';

    /**
     * @var string
     */
    protected $query;

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
        $this->query = $question;
        $this->suggestions = $suggestions;
        $this->default = $default;
        $this->nullable = isset($this->default);
        parent::__construct();
    }

    public function getQuery(): string
    {
        return $this->query;
    }


    abstract public function parseAnswer(Session $message): ? Answer;

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
            'question' => $this->query,
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
        return $this->slots
            ?? $this->slots = new Collection($this->makeDefaultSlots());
    }

    protected function makeDefaultSlots() : array
    {
        return [
            static::SLOT_QUERY => $this->getQuery(),
            static::SLOT_SUGGESTIONS => $suggestions = $this->getSuggestions(),
            static::SLOT_SUGGESTION_STR => implode(',', $suggestions),
            static::SLOT_DEFAULT_CHOICE => $this->getDefaultChoice(),
            static::SLOT_DEFAULT_VALUE => $this->getDefaultValue(),
        ];
    }

    /**
     * 只应该保留对 parse answer 有用的信息, 而不需要保留其它信息.
     * @return array
     */
    public function __sleep()
    {
        return [
            'query',
            'suggestions',
            'default',
            'nullable',
        ];
    }

}