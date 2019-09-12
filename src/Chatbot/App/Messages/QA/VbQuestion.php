<?php


namespace Commune\Chatbot\App\Messages\QA;


use Commune\Chatbot\App\Components\Predefined\Dialogue\OrdinalInt;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\Framework\Messages\QA\AbsQuestion;
use Commune\Chatbot\Framework\Messages\Traits\Verbosely;
use Commune\Chatbot\OOHost\Session\Session;
use Illuminate\Support\Str;

/**
 * Verbose Question
 */
class VbQuestion extends AbsQuestion
{
    use Verbosely;

    const REPLY_ID = QuestionReplyIds::ASK;

    /**
     * 判断回答是否只允许用建议值.
     * @var bool
     */
    protected $onlySuggestion = false;

    /**
     * @var int|null|string
     */
    protected $defaultChoice;

    /**
     * VbQuestion constructor.
     * @param string $question
     * @param array $suggestions
     * @param int|string|null $defaultChoice
     * @param string|null $default
     */
    public function __construct(
        string $question,
        array $suggestions,
        $defaultChoice = null,
        string $default = null
    )
    {
        $this->defaultChoice = $defaultChoice;

        // default value is a choice?
        $defaultIsSuggestion = isset($defaultChoice)
            && isset($suggestions[$defaultChoice]);

        $default = $default
            ?? ($defaultIsSuggestion ? $suggestions[$defaultChoice] : null);

        parent::__construct($question, $suggestions, $default);
    }

    public function getDefaultValue()
    {
        return isset($this->answer)
            ? $this->answer->toResult()
            : $this->default;
    }

    public function getDefaultChoice()
    {
        return isset($this->answer)
            ? $this->answer->getChoice()
            : $this->defaultChoice;
    }

    public function parseAnswer(Session $session): ? Answer
    {
        $message = $session->incomingMessage->message;
        // 如果本来就是answer, 不再处理了.
        if ($message instanceof Answer) {
            return $message;
        }

        // 目前只有 verbose 作为回答来处理.
        if (!$message instanceof VerboseMsg) {
            return null;
        }

        if ($message->isEmpty()) {
            // 为空并允许, 则使用默认值.
            if ($this->isNullable()) {
                return $this->answer = $this->newAnswer(
                    $message,
                    $this->getDefaultValue(),
                    null
                );

            // 为空不允许
            } else {
                return null;
            }
        }



        return $this->answer = $this->parseAnswerByOrdinal($session)
            ?? $this->doParseAnswer($message);
    }

    protected function parseAnswerByOrdinal(Session $session) : ? Answer
    {
        /**
         * @var OrdinalInt $ordinal
         */
        $ordinal = $session->getPossibleIntent(OrdinalInt::getContextName());
        if (isset($ordinal) && isset($ordinal->ordinal)) {
            $index = $ordinal->ordinal[0];
            if (empty($index)) {
                return null;
            }

            $max = count($this->suggestions);
            $abs = abs($index);
            if ($abs > 0 && $abs <= $max) {
                $indexes = array_keys($this->suggestions);
                $suggestions = array_values($this->suggestions);
                $order = $index > 0 ? $index -1 : ($max + $index);
                return $this->newAnswer(
                    $session->incomingMessage->message,
                    $suggestions[$order],
                    $indexes[$order]
                );
            }
        }
        return null;
    }

    protected function doParseAnswer(Message $message) : ? Answer
    {
        return $this->isIndexOfSuggestions($message)
            ?? $this->isSuggestionStartPart($message)
            ?? $this->acceptAnyAnswer($message)
            ?? null;
    }

    protected function acceptAnyAnswer(Message $message) : ? Answer
    {
        // 看看是否只允许在建议中.
        if (!$this->onlySuggestion) {
            return $this->newAnswer($message, $message->getTrimmedText(), null);
        }
        return null;
    }

    protected function isSuggestionStartPart(Message $message) : ? Answer
    {
        $text = $message->getTrimmedText();
        // 再匹配suggestions 的开头
        foreach ($this->suggestions as $index => $suggestion) {
            if (Str::startsWith($suggestion, $text)) {
                return $this->newAnswer(
                    $message,
                    $this->suggestions[$index],
                    $index
                );
            }
        }

        return null;
    }

    protected function isIndexOfSuggestions(Message $message) : ? Answer
    {
        $text = $message->getTrimmedText();
        $text = strtolower($text);
        $originIndexes = [];
        foreach ($this->suggestions as $index => $suggestion) {
            if (is_string($index)) {
                $originIndexes[strtolower($index)] = $index;
            } else {
                $originIndexes[$index] = $index;
            }
        }

        if (isset($originIndexes[$text])) {
            $originIndex = $originIndexes[$text];
            return $this->newAnswer(
                $message,
                $this->suggestions[$originIndex],
                $originIndex
            );
        }

        return null;
    }

    /**
     * @param Message $origin
     * @param mixed $value
     * @param null|int|string $choice
     * @return VbAnswer
     */
    protected function newAnswer(Message $origin, $value,  $choice = null) : VbAnswer
    {
        return new VbAnswer($origin, strval($value), $choice);
    }

    public function getAnswer(): ? Answer
    {
        return $this->answer;
    }

    public function __sleep()
    {
        $properties = parent::__sleep();
        $properties[] = 'defaultChoice';
        return $properties;
    }
}