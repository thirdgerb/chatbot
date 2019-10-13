<?php


namespace Commune\Chatbot\OOHost\Context\Stages;

use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Blueprint\Message\ReplyMsg;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Dialogue\DialogSpeechImpl;

/**
 * implements dialogSpeech
 * @property DialogSpeechImpl $dialogSpeech
 */
trait StageSpeechTrait
{
    public function callDialogSpeech(string $method, array $args)
    {
        if ($this->isAvailable()) {
            call_user_func_array([$this->dialogSpeech, $method], $args);
        }
        return $this;
    }

    public function withSlots(array $slots)
    {
        return $this->callDialogSpeech(__FUNCTION__, func_get_args());
    }


    public function beginParagraph()
    {
        return $this->callDialogSpeech(__FUNCTION__, func_get_args());
    }

    public function endParagraph()
    {
        return $this->callDialogSpeech(__FUNCTION__, func_get_args());
    }

    public function withContext(Context $from = null, array $keys = [])
    {
        return $this->callDialogSpeech(__FUNCTION__, func_get_args());
    }



    public function info($message, array $slots = [])
    {
        return $this->callDialogSpeech(__FUNCTION__, func_get_args());
    }

    public function debug($message, array $slots = [])
    {
        return $this->callDialogSpeech(__FUNCTION__, func_get_args());
    }

    public function warning($message, array $slots = [])
    {
        return $this->callDialogSpeech(__FUNCTION__, func_get_args());
    }

    public function notice($message, array $slots = [])
    {
        return $this->callDialogSpeech(__FUNCTION__, func_get_args());
    }

    public function error($message, array $slots = [])
    {
        return $this->callDialogSpeech(__FUNCTION__, func_get_args());
    }

    public function trans(string $message, array $slots = []): string
    {
        return $this->dialogSpeech->trans($message, $slots);
    }

    public function withReply(ReplyMsg $reply)
    {
        return $this->callDialogSpeech(__FUNCTION__, func_get_args());
    }

    public function ask(Question $question)
    {
        return $this->callDialogSpeech(__FUNCTION__, func_get_args());
    }

    public function askVerbose(
        string $question,
        array $suggestions = []
    )
    {
        return $this->callDialogSpeech(__FUNCTION__, func_get_args());
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
        return $this->callDialogSpeech(__FUNCTION__, func_get_args());
    }

    public function askSelects(
        string $question,
        array $suggestions,
        string $default = null
    )
    {
        return $this->callDialogSpeech(__FUNCTION__, func_get_args());
    }

    public function askConfirm(
        string $question,
        bool $default = true,
        string $yes = null,
        string $no = null
    )
    {
        return $this->callDialogSpeech(__FUNCTION__, func_get_args());
    }

    public function askIntentEntity(
        string $question,
        IntentMessage $intent,
        string $entityName,
        $default = null
    )
    {
        if ($this->isAvailable()) {
            call_user_func_array([$this->dialogSpeech, __FUNCTION__], func_get_args());
        }
        return $this;
    }

    public function askConfirmIntent(string $question, IntentMessage $intent)
    {
        if ($this->isAvailable()) {
            call_user_func_array([$this->dialogSpeech, __FUNCTION__], func_get_args());
        }
        return $this;
    }

    public function askConfirmEntity(string $question, IntentMessage $intent, string $entityName)
    {
        if ($this->isAvailable()) {
            call_user_func_array([$this->dialogSpeech, __FUNCTION__], func_get_args());
        }
        return $this;
    }


    public function askChooseIntents(
        string $question,
        array $options,
        array $intentNames,
        $defaultChoice = null
    )
    {
        if ($this->isAvailable()) {
            call_user_func_array([$this->dialogSpeech, __FUNCTION__], func_get_args());
        }
        return $this;
    }

    public function askSelectEntity(
        string $question,
        IntentMessage $intent,
        string $entityName,
        array $suggestion,
        $defaultChoice = null,
        bool $multiple = false
    )
    {
        if ($this->isAvailable()) {
            call_user_func_array([$this->dialogSpeech, __FUNCTION__], func_get_args());
        }
        return $this;
    }


    public function askChooseEntity(
        string $question,
        IntentMessage $intent,
        string $entityName,
        array $suggestions
    )
    {
        if ($this->isAvailable()) {
            call_user_func_array([$this->dialogSpeech, __FUNCTION__], func_get_args());
        }
        return $this;
    }
}