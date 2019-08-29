<?php


namespace Commune\Chatbot\App\Messages\QA;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Framework\Messages\Verbose;

/**
 * 多选.
 * @property Selection $answer
 */
class Selects extends Choose implements Question
{
    const QUESTION_ID = 'question.selects';

    protected $onlySuggestion = true;

    protected $separator;

    protected $defaultAllChoices = [];

    public function __construct(
        string $question,
        array $suggestions,
        array $defaultChoices = [],
        string $separator = ','
    )
    {
        $this->separator = $separator;
        $this->defaultAllChoices = $defaultChoices;

        $default = null;
        if (!empty($defaultChoices)) {
            $default = implode($separator, array_map(function($choice) use ($suggestions){
                return $suggestions[$choice];
            }, $defaultChoices));
        }

        parent::__construct(
            $question,
            $suggestions,
            null
        );

        $this->default = $default;
    }


    public function getDefaultValue()
    {
        return $this->default;
    }

    public function parseAnswer(Message $message): ? Answer
    {
        $text = $message->getTrimmedText();
        $choices = explode($this->separator, $text);

        $answers = array_map(function(string $str){
            return new Verbose($str);
        }, $choices);

        foreach ($answers as $answer) {
            parent::parseAnswer($answer);
        }

        return $this->answer;
    }

    protected function newAnswer(Message $origin, string $value, $choice = null): VbAnswer
    {
        if (!isset($this->answer)) {
            return new Selection($origin, $value, $choice);
        }

        $this->answer->addResult($value, $choice);
        return $this->answer;
    }


}