<?php

namespace Commune\Chatbot\OOHost\Dialogue;

use Commune\Chatbot\App\Messages\QA\Choose;
use Commune\Chatbot\App\Messages\QA\Confirm;
use Commune\Chatbot\App\Messages\QA\Contextual\AskEntity;
use Commune\Chatbot\App\Messages\QA\Contextual\ChooseEntity;
use Commune\Chatbot\App\Messages\QA\Contextual\ChooseIntent;
use Commune\Chatbot\App\Messages\QA\Contextual\ConfirmEntity;
use Commune\Chatbot\App\Messages\QA\Contextual\ConfirmIntent;
use Commune\Chatbot\App\Messages\QA\Contextual\SelectEntity;
use Commune\Chatbot\App\Messages\QA\Selects;
use Commune\Chatbot\App\Messages\QA\VbQuestion;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Blueprint\Message\ReplyMsg;
use Commune\Chatbot\Framework\Conversation\SpeechImpl;
use Commune\Chatbot\App\Messages\Replies\Reply;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\App\Messages\Replies\ParagraphText;
use Illuminate\Support\Collection;

class DialogSpeechImpl extends SpeechImpl implements DialogSpeech
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
     * @var ParagraphText
     */
    protected $paragraph;

    /**
     * DialogTalk constructor.
     * @param Dialog $dialog
     * @param array $slots
     */
    public function __construct(Dialog $dialog, array $slots = [])
    {
        $this->dialog = $dialog;
        $this->slots = $slots;
        parent::__construct($dialog->session->conversation);
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


    public function beginParagraph()
    {
        $this->paragraph = new ParagraphText([]);
        return $this;
    }

    public function endParagraph()
    {
        $this->conversation->reply($this->paragraph);
        $this->paragraph = null;
        return $this;
    }

    public function withReply(ReplyMsg $reply)
    {
        if (isset($this->paragraph)) {
            $this->paragraph->add($reply);
        } else {
            $this->conversation->reply($reply);
        }
    }


    public function log(string $level, $message, array $slots = array()) : void
    {
        $this->withReply(new Reply(
            strval($message),
            new Collection($this->mergeSlots($slots)),
            $level
        ));
    }

    public function ask(Question $question)
    {
        $question->mergeSlots($this->slots);
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

    public function askConfirm(
        string $question,
        bool $default = true,
        string $yes = null,
        string $no = null
    )
    {
        $yes = $yes ?? $this->dialog->session->chatbotConfig->defaultMessages->yes;
        $no = $no ?? $this->dialog->session->chatbotConfig->defaultMessages->no;
        $question = new Confirm($question, $default, $yes, $no);
        $result = $this->ask($question);
        return $result;
    }

    public function trans(string $message, array $slots = []): string
    {
        return parent::trans($message, $this->mergeSlots($slots));
    }

    /**
     * 通过Intent 来匹配一个 entity.
     * 注意匹配答案返回值通常是 string, 要在Context 中进行类型转换.
     *
     * @param string $question
     * @param IntentMessage $intent
     * @param string $entityName
     * @param mixed|null $default  scalar value
     * @return static
     */
    public function askIntentEntity(string $question, IntentMessage $intent, string $entityName, $default = null)
    {
        return $this->ask(new AskEntity($question, $intent, $entityName, $default));
    }

    public function askConfirmIntent(string $question, IntentMessage $intent)
    {
        return $this->ask(new ConfirmIntent($question, $intent));
    }

    public function askConfirmEntity(string $question, IntentMessage $intent, string $entityName)
    {
        return $this->ask(new ConfirmEntity($question, $intent, $entityName));
    }

    public function askSelectEntity(
        string $question,
        IntentMessage $intent,
        string $entityName,
        array $suggestions
    )
    {
        return $this->ask(new SelectEntity($question, $intent, $entityName, $suggestions));
    }

    public function askChooseEntity(
        string $question,
        IntentMessage $intent,
        string $entityName,
        array $suggestions
    )
    {
        return $this->ask(new ChooseEntity($question, $intent, $entityName, $suggestions));
    }


    public function askChooseIntents(
        string $question,
        array $options,
        array $intentNames,
        $defaultChoice = null
    )
    {
        return $this->ask(new ChooseIntent($question, $options, $intentNames, $defaultChoice));
    }

}