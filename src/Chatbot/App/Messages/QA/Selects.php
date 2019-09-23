<?php


namespace Commune\Chatbot\App\Messages\QA;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Framework\Messages\Verbose;
use Commune\Chatbot\OOHost\Session\Session;

/**
 * 多选.
 * @property Selection $answer
 * @method Selection|null parseAnswer(Session $session, Message $message = null): ? Answer
 */
class Selects extends Choose implements Question
{
    const REPLY_ID = QuestionReplyIds::SELECTS;

    protected $onlySuggestion = true;

    protected $separator;

    protected $defaultAllChoices = [];

    protected $defaultAnswers = [];

    public function __construct(
        string $question,
        array $suggestions,
        array $defaultChoices = [],
        string $separator = ','
    )
    {
        $this->separator = $separator;
        $this->defaultAllChoices = $defaultChoices;

        $defaultAnswers = [];
        if (!empty($defaultChoices)) {
            foreach ($suggestions as $index => $value) {
                if (in_array($index, $defaultChoices)) {
                    $defaultAnswers[] = $value;
                }
            }
        }
        $this->defaultAnswers = $defaultAnswers;

        parent::__construct(
            $question,
            $suggestions,
            null
        );

        $this->default = implode($separator, $defaultAnswers);
    }


    public function getDefaultValue()
    {
        return $this->default;
    }

    public function doParseAnswer(Message $message): ? Answer
    {
        $this->answer = new Selection($message, [], []);

        $text = $message->getTrimmedText();

        // 将隔开的结果
        $choices = explode($this->separator, $text);
        $answers = array_map(function(string $str){
            return new Verbose($str);
        }, $choices);


        foreach ($answers as $answer) {
            parent::doParseAnswer($answer);
        }

        $choices = $this->answer->getChoices();
        if (empty($choices)) {
            if (empty($this->defaultAllChoices)) {
                return $this->answer = null;
            }

            return $this->answer = new Selection($message, $this->defaultAnswers, $this->defaultAllChoices);
        }

        return $this->answer;
    }

    protected function newAnswer(Message $origin, $value, $choice = null): VbAnswer
    {
        if (!isset($this->answer)) {
            $answers = isset($value) ? [$value] : [];
            $choices = isset($choice) ? [$choice] : [];
            return $this->answer = new Selection($origin, $answers, $choices);
        }

        $this->answer->addResult($value, $choice);
        return $this->answer;
    }

    public function __sleep()
    {
        $props = parent::__sleep();
        $props = array_merge($props, [
            'separator',
            'defaultAllChoices',
            'defaultAnswers',
        ]);
        return $props;
    }

}