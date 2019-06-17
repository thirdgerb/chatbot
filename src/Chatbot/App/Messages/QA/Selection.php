<?php


namespace Commune\Chatbot\App\Messages\QA;

use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\QA\Answer;

class Selection extends VbAnswer implements Answer
{
    protected $choices = [];

    protected $answers = [];

    public function __construct(
        Message $origin,
        string $answer,
        int $choice = null
    )
    {
        $this->answers[] = $answer;
        if (isset($choice)) {
            $this->choices[] = $choice;
        }
        parent::__construct($origin, $answer);
    }

    /**
     * @param int|string $choice
     * @return bool
     */
    public function hasChoice($choice): bool
    {
        return in_array($choice, $this->choices);
    }

    public function addResult(string $value, int $choice) : void
    {
        $this->answers[] = $value;
        $this->choices[] = $choice;
        $this->answer .= ',' . $value;
    }
}