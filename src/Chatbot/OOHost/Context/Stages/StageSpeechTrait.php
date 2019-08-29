<?php


namespace Commune\Chatbot\OOHost\Context\Stages;


use Commune\Chatbot\Blueprint\Conversation\Speech;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\DialogSpeechImpl;

/**
 * @property DialogSpeechImpl $dialogSpeech
 */
trait StageSpeechTrait
{

    public function withSlots(array $slots)
    {
        if ($this->isAvailable()) {
            $this->dialogSpeech->withSlots($slots);
        }
        return $this;
    }

    public function withContext(Context $from = null, array $keys = [])
    {
        if ($this->isAvailable()) {
            $this->dialogSpeech->withContext($from, $keys);
        }
        return $this;
    }

    public function info(string $message, array $slots = [])
    {
        if ($this->isAvailable()) {
            $this->dialogSpeech->info($message, $slots);
        }
        return $this;
    }

    public function debug(string $message, array $slots = [])
    {
        if ($this->isAvailable()) {
            $this->dialogSpeech->debug($message, $slots);
        }
        return $this;
    }

    public function warning(string $message, array $slots = [])
    {
        if ($this->isAvailable()) {
            $this->dialogSpeech->warning($message, $slots);
        }
        return $this;
    }

    public function notice(string $message, array $slots = [])
    {
        if ($this->isAvailable()) {
            $this->dialogSpeech->notice($message, $slots);
        }
        return $this;
    }

    public function error(string $message, array $slots = [])
    {
        if ($this->isAvailable()) {
            $this->dialogSpeech->error($message, $slots);
        }
        return $this;
    }

    public function trans(string $message, array $slots = []): string
    {
        return $this->dialogSpeech->trans($message, $slots);
    }

    public function ask(Question $question)
    {
        if ($this->isAvailable()) {
            $this->dialogSpeech->ask($question);
        }
        return $this;
    }

    public function askVerbose(
        string $question,
        array $suggestions = []
    )
    {
        if ($this->isAvailable()) {
            $this->dialogSpeech->askVerbose($question, $suggestions);
        }
        return $this;
    }

    /**
     * @param string $question
     * @param array $suggestions
     * @param null|string|int $default
     * @return $this
     */
    public function askChoose(
        string $question,
        array $suggestions,
        $default = null
    )
    {
        if ($this->isAvailable()) {
            $this->dialogSpeech->askChoose($question, $suggestions, $default);
        }
        return $this;
    }

    public function askSelects(
        string $question,
        array $suggestions,
        string $default = null
    )
    {
        if ($this->isAvailable()) {
            $this->dialogSpeech->askSelects($question, $suggestions, $default);
        }
        return $this;
    }

    public function askConfirm(
        string $question,
        bool $default = true,
        string $yes = 'y',
        string $no = 'n'
    )
    {
        if ($this->isAvailable()) {
            $this->dialogSpeech
                ->askConfirm(
                    $question,
                    $default,
                    $yes,
                    $no
                );
        }
        return $this;
    }


}