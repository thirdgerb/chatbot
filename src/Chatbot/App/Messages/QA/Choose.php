<?php


namespace Commune\Chatbot\App\Messages\QA;


use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;

class Choose extends VbQuestion implements Question
{

    protected $onlySuggestion = true;

    /**
     * @var string|int|null
     */
    protected $defaultChoice;

    /**
     * Choose constructor.
     * @param string $question
     * @param array $options
     * @param null $defaultChoice
     * @param null $default
     */
    public function __construct(
        string $question,
        array $options,
        $defaultChoice = null,
        $default = null
    )
    {
        if (
            empty($options)
            || (isset($defaultChoice) && !array_key_exists($defaultChoice, $options))
        ) {
            throw new ConfigureException(
                static::class
                . ' choose must have options'
            );
        }

        parent::__construct($question, $options, $defaultChoice, $default);
    }

    protected function newAnswer(Message $origin, string $value, $choice = null) : VbAnswer
    {
        $choice = $choice ?? $this->defaultChoice;
        return new Choice($origin, $value, $choice);
    }
}