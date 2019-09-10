<?php


namespace Commune\Chatbot\App\Messages\Templates;


use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\ReplyTemplate;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Blueprint\Message\ReplyMsg;
use Commune\Chatbot\Contracts\Translator;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;

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
            return $this->renderQuestion($reply, $conversation);
        }

        throw new ConfigureException(
            static::class
            . ' only accept QuestionMsg'
        );
    }


    /**
     * @param Question $question
     * @param Conversation $conversation
     * @return array
     */
    protected function renderQuestion(Question $question, Conversation $conversation) : array
    {
        $query = $this->renderQuery($question);
        $default = $this->renderDefault($question);
        $suggestion = $this->renderSuggestionStr($question);


        $text = $this->composeText($query, $suggestion, $default);

        return $this->wrapText($question, $text);
    }

    protected function composeText(string $query, string $suggestion, string $default) : string
    {
        return $this->translator->trans('question.default', [
            'query' => $query,
            'suggestions' => $suggestion,
            'default' => $default,
        ]);
    }

    protected function wrapText(Question $question, string $text) : array
    {
        $message = (new Text($text))->withLevel($question->getLevel());
        return [ $message ];
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