<?php


namespace Commune\Chatbot\App\Messages\Templates;


use Commune\Chatbot\Blueprint\Message\QA\Question;

class ConfirmTemp extends QuestionTemp
{

    protected function renderDefault(Question $question): string
    {
        $default = $question->getDefaultValue();

        if (!isset($default)) {
            return '';
        }

        $default = $this->translator->trans($default, $question->getSlots()->all());

        return  " ($default)";
    }

    protected function renderSuggestionStr(Question $question, array $suggestions): string
    {
        return " [{$suggestions[1]}|{$suggestions[0]}]";
    }

    protected function composeText(
        string $question,
        string $default,
        string $suggestion
    ): string
    {
        return "$question$suggestion$default";
    }

}