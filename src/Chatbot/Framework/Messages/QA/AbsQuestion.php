<?php


namespace Commune\Chatbot\Framework\Messages\QA;


use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Messages\AbsMessage;

abstract class AbsQuestion extends AbsMessage implements Question
{
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
     * @var string
     */
    protected $text;

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

    abstract public function parseAnswer(Message $message): ? Answer;

    abstract public function makeQuestion(): string;

    public function getText(): string
    {
        return $this->text ?? $this->text = $this->makeQuestion();
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function suggestions(): array
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
            'question' => $this->question,
            'suggestions' => $this->suggestions,
            'default' => $this->getDefaultValue(),
            'nullable' => $this->nullable,
            'answer' => isset($this->answer) ? $this->answer->toArray() : null
        ];
    }

    public function namesAsDependency(): array
    {
        return array_merge(
            parent::namesAsDependency(),
            [Question::class, AbsQuestion::class]
        );
    }


}