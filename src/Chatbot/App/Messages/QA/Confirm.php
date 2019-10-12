<?php


namespace Commune\Chatbot\App\Messages\QA;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\QA\Answer;

class Confirm extends VbQuestion
{
    const REPLY_ID = QuestionReplyIds::CONFIRM;

    const YES_DEFAULT = 'y';
    const NO_DEFAULT = 'n';

    protected $onlySuggestion = true;

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

    protected function isInSuggestions(Message $message, string $text): ? Answer
    {
        $defaults = [
            static::YES_DEFAULT => 1,
            static::NO_DEFAULT => 0,
        ];

        if (isset($defaults[$text])) {
            $choice = $defaults[$text];
            return $this->newAnswer($message, $this->suggestions[$choice], $choice);
        }

        return parent::isInSuggestions($message, $text);
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