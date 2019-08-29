<?php


namespace Commune\Chatbot\App\Messages\Templates;


use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\ReplyTemplate;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Blueprint\Message\ReplyMsg;
use Commune\Chatbot\Contracts\Translator;

/**
 * default message template for verbose question
 *
 * generate verbose text message
 */
class QuestionTemp implements ReplyTemplate
{

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * QuestionTemp constructor.
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }


    public function render(ReplyMsg $reply, Conversation $conversation): array
    {
        if ($reply instanceof Question) {
            return $this->renderQuestion($reply);
        }

        throw new \InvalidArgumentException(
            static::class
            . ' only accept QuestionMsg'
        );
    }


    /**
     * @param Question $question
     * @return array
     */
    protected function renderQuestion(Question $question) : array
    {
        $query = $this->renderQuery($question);
        $default = $this->renderDefault($question);
        $suggestion = $this->renderSuggestionStr($question);

        $text = $this->composeText($query, $default, $suggestion);

        return $this->wrapText($question, $text);
    }

    protected function wrapText(Question $question, string $text) : array
    {
        $message = (new Text($text))->withLevel($question->getLevel());
        return [ $message ];
    }

    protected function composeText(
        string $question,
        string $default,
        string $suggestion
    ) : string
    {
        return "$question$default\n$suggestion";
    }

    protected function renderDefault(Question $question) : string
    {
        $default =  $question->getDefaultChoice() ?? $question->getDefaultValue();
        if (isset($default)) {
            return ' (' . strval($default) . ')';
        }
        return '';
    }

    protected function renderQuery(Question $question) : string
    {
        $slots = $question->getSlots();
        return $this->translator->trans($question, $slots->all());
    }

    protected function renderSuggestionStr(Question $question) : string
    {
        $suggestions = $question->getSuggestions();
        $slots = $question->getSlots();

        $str = '';
        if (!empty($suggestions)) {
            foreach ($suggestions as $index => $suggestion) {
                $suggestion = $this->translator->trans($suggestion, $slots->all());
                $str .= PHP_EOL . "[$index] $suggestion";
            }
        }
        return $str;
    }


}