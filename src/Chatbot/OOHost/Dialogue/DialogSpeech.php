<?php

namespace Commune\Chatbot\OOHost\Dialogue;

use Commune\Chatbot\App\Messages\QA\Choose;
use Commune\Chatbot\App\Messages\QA\Confirm;
use Commune\Chatbot\App\Messages\QA\Selects;
use Commune\Chatbot\App\Messages\QA\VbQuestion;
use Commune\Chatbot\Blueprint\Conversation\Monologue;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\OOHost\Context\Context;

class DialogSpeech implements Speech
{

    /**
     * @var Dialog
     */
    protected $dialog;

    /**
     * @var array
     */
    protected $slots = [];

    /**
     * @var Monologue|null
     */
    protected $monologue;

    /**
     * DialogTalk constructor.
     * @param Dialog $dialog
     * @param array $slots
     */
    public function __construct(Dialog $dialog, array $slots = [])
    {
        $this->dialog = $dialog;
        $this->slots = $slots;
    }


    /*-------- talk --------*/

    public function withSlots(array $slots)
    {
        $this->slots = $this->mergeSlots($slots);
        return $this;
    }


    public function withContext(Context $from = null, array $keys = [])
    {
        $from = $from ?? $this->dialog->currentContext();

        $slots = $from->toAttributes();

        foreach ($keys as $key) {
            $value = $from->__get($key);
            $slots[$key] = $value;
        }
        $this->slots = $this->slots + $slots;
        return $this;
    }

    protected function mergeSlots(array $slots = []) : array
    {
        return $slots + $this->slots;
    }

    /**
     * @return Monologue
     */
    protected function getMonolog()
    {
        return $this->monologue ?? $this->monologue = $this->dialog
                ->session
                ->conversation
                ->monolog();
    }

    public function debug(string $message, array $slots = []) 
    {
        $this->getMonolog()->debug($message, $this->mergeSlots($slots));
        return $this;
    }

    public function info(string $message, array $slots = []) 
    {
        $this->getMonolog()->info($message, $this->mergeSlots($slots));
        return $this;
    }

    public function warning(string $message, array $slots = []) 
    {
        $this->getMonolog()->warning($message, $this->mergeSlots($slots));
        return $this;
    }

    public function notice(string $message, array $slots = []) 
    {
        $this->getMonolog()->notice($message, $this->mergeSlots($slots));
        return $this;
    }

    public function error(string $message, array $slots = []) 
    {
        $this->getMonolog()->error($message, $this->mergeSlots($slots));
        return $this;
    }

    public function ask(Question $question)
    {
        if ($question instanceof VerboseMsg) {
            $question->withSlots($this->slots);
        }
        $this->dialog->reply($question);
        return $this;
    }


    public function askVerbose(
        string $question,
        array $suggestions = []
    )
    {
        $question = new VbQuestion($question, $suggestions);
        return $this->ask($question);
    }

    public function askChoose(
        string $question,
        array $suggestions,
        $default = null
    )
    {
        $question = new Choose($question, $suggestions, $default);
        return $this->ask($question);
    }

    public function askSelects(
        string $question,
        array $suggestions,
        string $default = null
    )
    {
        $question = new Selects($question, $suggestions, $default);
        return $this->ask($question);
    }

//    public function askMessageTypes(
//        string $question,
//        array $allowedTypes
//    ): Talk
//    {
//    }

    public function askConfirm(
        string $question,
        bool $default = true,
        string $yes = 'y',
        string $no = 'n'
    )
    {
        $question = new Confirm($question, $default, $yes, $no);
        $result = $this->ask($question);
        return $result;
    }

    public function trans(string $message, array $slots = []): string
    {
        return $this->getMonolog()->trans($message, $this->mergeSlots($slots));
    }


}