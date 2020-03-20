<?php


namespace Commune\Chatbot\App\Messages\QA;

use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\QA\Answer;

/**
 * @method null getChoice()
 */
class Selection extends VbAnswer implements Answer
{
    /**
     * 多个选项.
     * @var array
     */
    protected $choices = [];

    /**
     * 多个答案
     * @var array
     */
    protected $results = [];

    public function __construct(
        Message $origin,
        array $answers,
        array $choices
    )
    {
        $this->results = $answers;
        $this->choices = $choices;

        parent::__construct($origin, implode(',', $answers));
    }

    public function __sleep(): array
    {
        return array_merge(parent::__sleep(), ['choices', 'results']);
    }

    /**
     * @return array
     */
    public function getChoices(): array
    {
        return $this->choices;
    }

    /**
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }


    /**
     * @param int|string $choice
     * @return bool
     */
    public function hasChoice($choice): bool
    {
        return in_array($choice, $this->choices);
    }

    /**
     *
     * @param string $value
     * @param int $choice
     */
    public function addResult(string $value, int $choice) : void
    {
        $this->results[] = $value;
        $this->choices[] = $choice;
    }

    /**
     * @return string
     */
    public function toResult()
    {
        return implode(',', $this->results);
    }

    public static function mock()
    {
        return new Selection(new Text('abc'), ['a', 'b', 'c'], [0, 1, 2]);
    }
}