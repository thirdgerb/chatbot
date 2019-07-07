<?php


namespace Commune\Chatbot\OOHost\Directing\Dialog;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\History\History;

class Hear extends AbsNavigator
{
    /**
     * @var Message
     */
    protected $message;


    public function __construct(Dialog $dialog, History $history, Message $message)
    {
        $this->message = $message;
        parent::__construct($dialog, $history);
    }

    public function doDisplay(): ? Navigator
    {
        $question = $this->dialog->currentQuestion();
        if (isset($question)) {
            $answer = $question->parseAnswer($this->message);
            if (isset($answer)) {
                $this->message = $answer;
            }
        }

        return $this->callbackCurrent($this->message);
    }


}