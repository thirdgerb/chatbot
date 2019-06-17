<?php


namespace Commune\Chatbot\App\Messages\QA;


use Commune\Chatbot\Blueprint\Message\Message;

class Confirm extends VbQuestion
{
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

    protected function makeText(string $question, array $suggestions): string
    {
        $question .= " [{$suggestions[1]}|{$suggestions[0]}]";
        if ($this->hasDefault()) {
            $question .= ' (' . $this->getDefaultValue(). ')';
        }
        return $question;
    }

    protected function newAnswer(Message $origin, string $value, $choice = null): VbAnswer
    {
        $choice = $choice ?? $this->defaultChoice;
        return new Confirmation($origin, $value, $choice);
    }
}