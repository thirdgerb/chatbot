<?php


namespace Commune\Chatbot\App\Messages\QA;


use Commune\Chatbot\Blueprint\Message\Message;

class Confirm extends VbQuestion
{
    const REPLY_ID = QuestionReplyIds::CONFIRM;

    protected $onlySuggestion = true;

    protected $defaultChoice;

    public function __construct(
        string $question,
        bool $default = true,
        string $positive = 'y',
        string $negative = 'n'
    )
    {
        $defaultChoice = isset($default) ? ($default ? 1 : 0) : null;
        $this->defaultChoice = $defaultChoice;
        $defaultValue = isset($default)
            ? ($default ? $positive : $negative)
            : null;
        parent::__construct(
            $question,
            [$negative, $positive],
            $defaultChoice,
            $defaultValue
        );
    }

    /**
     * @param Message $origin
     * @param string $value
     * @param int|null $choice
     * @return VbAnswer
     */
    protected function newAnswer(Message $origin, $value, $choice = null): VbAnswer
    {
        $choice = $choice ?? $this->defaultChoice;
        return new Confirmation($origin, strval($value), $choice);
    }
}